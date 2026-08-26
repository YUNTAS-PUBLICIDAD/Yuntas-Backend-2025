<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Canal General / Web
     */
    public function handle(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|exists:chatbot_conversations,uuid',
            'lead_id' => 'nullable|exists:leads,id'
        ]);

        // 1. Obtener o crear la conversación usando UUID
        $conversation = $this->resolveConversation($request);

        Log::info('Con conversación resuelta (Web)', [
            'conversation_id' => $conversation->id,
            'uuid' => $conversation->uuid,
            'lead_id' => $conversation->lead_id,
        ]);

        // 2. Enviar el mensaje a n8n pasándole el UUID como sessionId para mantener el historial
        $reply = $this->callN8nEngine($request->message, $conversation->uuid);

        // TODO: Si necesitas guardar el mensaje en tu base de datos local (ej. $conversation->messages()->create(...)), puedes hacerlo aquí usando $reply.

        return response()->json([
            'conversation_id' => $conversation->uuid,
            'messages' => [
                [
                    'role' => 'bot',
                    'sender' => 'bot',
                    'text' => $reply['response'],
                    'type' => $reply['show_whatsapp'] ? 'whatsapp' : 'text',
                    'whatsapp_url' => $reply['show_whatsapp']
                        ? 'https://wa.me/51912849782?text=' . urlencode('Hola Yuntas, quisiera conversar con un asesor comercial para que me brinde más información, por favor.')
                        : null,
                ]
            ],
        ]);
    }

    /**
     * Canal WhatsApp
     */
    public function whatsapp(Request $request)
    {
        $phone = $request->phone;
        $message = $request->message;

        $cleanPhone = $this->normalizePhone($phone);

        if (!preg_match('/^9\d{8}$/', $cleanPhone)) {
            return response()->json([
                'error' => 'Número inválido'
            ], 400);
        }

        // 1. Buscar por número en lead o crearlo
        $lead = Lead::firstOrCreate(
            ['phone' => $cleanPhone],
            [
                'name' => 'Usuario Whatsapp',
                'email' => 'temp_' . $cleanPhone . '@noemail.com',
                'source_id' => null
            ]
        );

        // 2. Conversación siempre ligada al lead y al canal whatsapp
        $conversation = ChatbotConversation::firstOrCreate(
            [
                'channel' => 'whatsapp',
                'external_id' => $cleanPhone
            ],
            [
                'lead_id' => $lead->id,
                'started_at' => now(),
                'context' => []
            ]
        );

        // 3. Garantizar consistencia de la relación
        if ($conversation->lead_id !== $lead->id) {
            $conversation->update(['lead_id' => $lead->id]);
        }

        Log::info('Con conversación resuelta (WhatsApp)', [
            'conversation_id' => $conversation->id,
            'uuid' => $conversation->uuid,
            'phone' => $cleanPhone
        ]);

        // 4. Enviar a n8n usando el UUID de la conversación como identificador único
        $reply = $this->callN8nEngine($message, $conversation->uuid);

        return response()->json([
            'reply' => $reply['response'],
            'show_whatsapp' => $reply['show_whatsapp'],
        ]);
    }

    /**
     * Servicio interno para conectar con el Webhook de n8n
     */
    protected function callN8nEngine(string $message, string $sessionId): array
    {
        $responseText = null;
        $showWhatsapp = false;
        $n8nUrl = config('services.n8n.webhook_url');

        if (!$n8nUrl) {
            Log::error("La variable N8N_WEBHOOK_URL no está definida en el archivo .env o config/services.php.");
        } else {
            try {
                $http = Http::timeout(10); // Timeout preventivo de 10 segundos

                if (app()->environment('local')) {
                    $http = $http->withoutVerifying();
                }

                // Payload idéntico a tu referencia, adaptado a tus variables
                $response = $http->post($n8nUrl, [
                    'chatbotYuntas' => $message,
                    'sessionId' => $sessionId,
                    'platform' => 'website'
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // Extrae la respuesta buscando llaves comunes devueltas por flujos de n8n AI
                    $responseText = $data['response']
                        ?? $data['output']
                        ?? $data['respuesta']
                        ?? null;
                    $showWhatsapp = $data['show_whatsapp'] ?? false;
                } else {
                    Log::error('n8n respondió con código de error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error de comunicación con n8n: ' . $e->getMessage());
            }
        }

        // Fallback en caso de que n8n falle o no devuelva el formato esperado
        if (empty($responseText)) {
            $responseText = "Por el momento no puedo procesar tu solicitud, por favor comunícate directamente con soporte.";
        }

        return [
            'response' => $responseText,
            'show_whatsapp' => $showWhatsapp,
        ];
    }

    /**
     * Resolver la conversación por UUID o crear una nueva si no viene en el Request
     */
    protected function resolveConversation($request)
    {
        if ($request->conversation_id) {
            return ChatbotConversation::where('uuid', $request->conversation_id)->firstOrFail();
        }

        return ChatbotConversation::create([
            'lead_id' => $request->lead_id,
            'started_at' => now(),
            'context' => []
        ]);
    }

    /**
     * Limpieza de números telefónicos
     */
    protected function normalizePhone($phone)
    {
        $clean = preg_replace('/\D/', '', $phone);
        if (strlen($clean) === 11 && str_starts_with($clean, '51')) {
            return substr($clean, 2);
        }
        return $clean;
    }
}