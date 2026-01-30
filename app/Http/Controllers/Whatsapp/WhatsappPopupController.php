<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\Lead;
use App\Models\WhatsappPopup;
use App\Models\Product;
use App\Models\WhatsappProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsappPopupController extends Controller
{
    private $whatsappServiceUrl;

    public function __construct() {
        $this->whatsappServiceUrl = env('WHATSAPP_SERVICE_URL', 'http://localhost:3001');
    }

    // Enviar mensaje de WhatsApp a un lead específico
    public function enviar(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'message' => 'nullable|string',
            'product_id' => 'nullable|integer',
            'source_id' => 'required|integer',
        ]);

        // Buscar lead existente
        $leadExistente = Lead::where('email', $request->email)->first();
        $phoneChanged = $leadExistente && $leadExistente->phone !== $request->phone;

        // Actualizar o crear lead
        $lead = Lead::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'message' => $request->message,
                'product_id' => $request->product_id,
                'source_id' => $request->source_id,
            ]
        );

        if ($phoneChanged) { // si phone cambia, se limpia chat_id en mensajes de WhatsApp
            WhatsappMessage::where('lead_id', $lead->id)
                ->whereNotNull('chat_id')
                ->update(['chat_id' => null]);
        }

        // Validar que tenga teléfono
        if (!$lead->phone) {
            return response()->json([
                'success' => false,
                'message' => 'El lead no tiene número de teléfono'
            ], 422);
        }

        // Obtener plantilla según el popup del que viene el lead
        $plantillaPopup = WhatsappPopup::activaPorSource($request->source_id);

        if (!$plantillaPopup) {
            return response()->json([
                'success' => false,
                'message' => 'No hay plantilla configurada para esta fuente'
            ], 500);
        }

        // variables para el mensaje
        $variables = [
            'nombre' => $lead->name,
        ];

        // Si es detalle de producto, agregar variables y obtener imagen
        $imagenUrl = null;
        if ($lead->product_id && $lead->source->name === 'Producto detalle') {
            $imagenUrl = $lead->product->images
                                    ->where('slot_id', 5) // Slot de imagen principal
                                    ->first()?->url;

            // variables adicionales
            $variables = array_merge($variables, [
                'producto_nombre' => $lead->product->name ?? '',
                'descripcion' => $lead->product->description ?? '',
                'fecha' => now('America/Lima')->format('d/m/Y'),
                'hora' => now('America/Lima')->format('H:i'),
                'email' => $lead->email,
            ]);
        } else { // Para popup de Inicio y Productos, se usa imagen de la plantilla
            $imagenUrl = $plantillaPopup->imagen_url;
        }

        // Enviar mensaje al lead
        $resultado = $this->enviarWhatsappALead(
            $lead, 
            $plantillaPopup, 
            $variables,
            $imagenUrl
        );

        if (!$resultado['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar mensaje de WhatsApp'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mensaje de WhatsApp enviado correctamente',
            'lead' => [
                'id' => $lead->id,
                'nombre' => $lead->name,
                'phone' => $lead->phone,
            ]
        ]);
    }

    private function enviarWhatsappALead($lead, $plantilla, array $variables, ?string $imagenProducto)
    {
        try {

            $mensaje = $plantilla->procesarVariables($variables);
            $imagenUrl = $imagenProducto;

            // Verificar si podemos omitir validación en el servicio de WhatsApp
            $ultimoMensajeExitoso = WhatsappMessage::where('lead_id', $lead->id)
                ->where('status', 'enviado')
                ->latest('sent_at')
                ->first();

            $skipValidation = false;
            $chatId = null;

            if ($ultimoMensajeExitoso) {
                // Verificar que no haya fallidos después del último exitoso
                $hayFallidosRecientes = WhatsappMessage::where('lead_id', $lead->id)
                    ->where('status', 'fallido')
                    ->where('sent_at', '>', $ultimoMensajeExitoso->sent_at)
                    ->exists();

                if (!$hayFallidosRecientes) {
                    $skipValidation = true;
                    $chatId = $ultimoMensajeExitoso->chat_id;
                }
            }

            // Preparar payload base
            $payload = [
                'phone' => strlen($lead->phone) === 9 ? '51' . $lead->phone : $lead->phone,
            ];

            if ($skipValidation) {
                $payload['skipValidation'] = true;
                if ($chatId) {
                    $payload['chatId'] = $chatId;
                }
            }

            // Si tiene imagen, enviar con imagen
            if ($imagenUrl) {
                $imagePath = str_replace('storage/', '', $imagenUrl);
                $image = Storage::disk('public')->get($imagePath);

                $payload['imageData'] = base64_encode($image);
                $payload['caption'] = $mensaje;

                Log::info('Enviando WhatsApp con imagen', [
                    'lead_id' => $lead->id,
                    'phone' => $payload['phone'],
                    'tiene_imagen' => true
                ]);

                $response = Http::timeout(30)->post("{$this->whatsappServiceUrl}/api/whatsapp/send-image", $payload);
            } else {
                $payload['message'] = $mensaje;

                Log::info('Enviando WhatsApp solo texto', [
                    'lead_id' => $lead->id,
                    'phone' => $payload['phone']
                ]);

                $response = Http::timeout(30)->post("{$this->whatsappServiceUrl}/api/whatsapp/send-message", $payload);
            }

            $success = $response->json()['success'] ?? false;
            $responseChatId = $response->json()['chatId'] ?? null;

            // Guardar registro del mensaje
            WhatsappMessage::create([
                'lead_id' => $lead->id,
                'type' => 'popup',
                'body' => $mensaje,
                'status' => $success ? 'enviado' : 'fallido',
                'image_url' => $imagenUrl,
                'sent_at' => now(),
                'chat_id' => $responseChatId,
                'error_message' => $success ? null : ($response->json()['message'] ?? 'Error desconocido'),
            ]);

            return ['success' => $success];

        } catch (\Exception $e) {
            Log::error('Error enviando WhatsApp', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Guardar registro del error
            WhatsappMessage::create([
                'lead_id' => $lead->id,
                'type' => 'popup',
                'body' => $mensaje ?? '',
                'status' => 'fallido',
                'chat_id' => null,
                'image_url' => $imagenUrl ?? null,
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}