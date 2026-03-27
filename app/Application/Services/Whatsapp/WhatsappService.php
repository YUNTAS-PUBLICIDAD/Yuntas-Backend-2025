<?php

namespace App\Application\Services\Whatsapp;

use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class WhatsappService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('WHATSAPP_SERVICE_URL', 'http://localhost:3001');
    }

    public function send($lead, string $mensaje, ?string $imagenUrl = null): array
    {
        try {
            // =========================
            // 🔁 Reutilizar chat si existe
            // =========================
            $ultimoMensajeExitoso = WhatsappMessage::where('lead_id', $lead->id)
                ->where('status', 'enviado')
                ->latest('sent_at')
                ->first();

            $skipValidation = false;
            $chatId = null;

            if ($ultimoMensajeExitoso) {
                $hayFallidosRecientes = WhatsappMessage::where('lead_id', $lead->id)
                    ->where('status', 'fallido')
                    ->where('sent_at', '>', $ultimoMensajeExitoso->sent_at)
                    ->exists();

                if (!$hayFallidosRecientes) {
                    $skipValidation = true;
                    $chatId = $ultimoMensajeExitoso->chat_id;
                }
            }

            // =========================
            // 📦 Payload base
            // =========================
            $payload = [
                'phone' => strlen($lead->phone) === 9
                    ? '51' . $lead->phone
                    : $lead->phone,
            ];

            if ($skipValidation) {
                $payload['skipValidation'] = true;
                if ($chatId) {
                    $payload['chatId'] = $chatId;
                }
            }

            // =========================
            // 🖼️ Envío con imagen
            // =========================
            if ($imagenUrl) {
                $parsedPath = parse_url($imagenUrl, PHP_URL_PATH);
                $imagePath = ltrim(str_replace('/storage/', '', $parsedPath), '/');

                $image = Storage::disk('public')->get($imagePath);

                $payload['imageData'] = base64_encode($image);
                $payload['caption'] = $mensaje;

                Log::info('WhatsApp: enviando imagen', [
                    'lead_id' => $lead->id,
                    'phone' => $payload['phone'],
                ]);

                $response = Http::timeout(30)
                    ->post("{$this->baseUrl}/api/whatsapp/send-image", $payload);

            } else {
                // =========================
                // 💬 Envío solo texto
                // =========================
                $payload['message'] = $mensaje;

                Log::info('WhatsApp: enviando texto', [
                    'lead_id' => $lead->id,
                    'phone' => $payload['phone'],
                ]);

                $response = Http::timeout(30)
                    ->post("{$this->baseUrl}/api/whatsapp/send-message", $payload);
            }

            $success = $response->json()['success'] ?? false;
            $responseChatId = $response->json()['chatId'] ?? null;

            // =========================
            // 🧾 Guardar log
            // =========================
            WhatsappMessage::create([
                'lead_id' => $lead->id,
                'type' => 'system',
                'body' => $mensaje,
                'status' => $success ? 'enviado' : 'fallido',
                'image_url' => $imagenUrl,
                'sent_at' => now(),
                'chat_id' => $responseChatId,
                'error_message' => $success
                    ? null
                    : ($response->json()['message'] ?? 'Error desconocido'),
            ]);

            return ['success' => $success];

        } catch (Exception $e) {
            Log::error('Error enviando WhatsApp', [
                'lead_id' => $lead->id ?? null,
                'error' => $e->getMessage(),
            ]);

            WhatsappMessage::create([
                'lead_id' => $lead->id ?? null,
                'type' => 'system',
                'body' => $mensaje ?? '',
                'status' => 'fallido',
                'chat_id' => null,
                'image_url' => $imagenUrl ?? null,
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendToPhone(string $phone, string $mensaje, ?string $imagenUrl = null):array
    {
      try {
        $payload = [
                   'phone' => strlen($phone) === 9 ? '51' . $phone : $phone,
               ];

               if ($imagenUrl) {
                   $parsedPath = parse_url($imagenUrl, PHP_URL_PATH);
                   $imagePath = ltrim(str_replace('/storage/', '', $parsedPath), '/');
                   $image = Storage::disk('public')->get($imagePath);

                   $payload['imageData'] = base64_encode($image);
                   $payload['caption'] = $mensaje;

                   $response = Http::timeout(30)->post("{$this->baseUrl}/api/whatsapp/send-image", $payload);
               } else {
                   $payload['message'] = $mensaje;

                   $response = Http::timeout(30)->post("{$this->baseUrl}/api/whatsapp/send-message", $payload);
               }

               return [
                   'success' => $response->json()['success'] ?? false
               ];
      }catch (Exception $e){
       Log::error('Error enviando WhatsApp (sin lead)', [
       'phone' => $phone,
       'error' => $e->getMessage(),
       ]);

       return [
       'success' => false,
       'error' => $e->getMessage()
       ];
      }
    }
}
