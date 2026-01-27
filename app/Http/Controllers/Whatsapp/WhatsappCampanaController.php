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

class WhatsappCampanaController extends Controller
{
    private $whatsappServiceUrl;

    public function __construct() {
        $this->whatsappServiceUrl = env('WHATSAPP_SERVICE_URL', 'http://localhost:3001');
    }

    // Ver estado de la conexion de WhatsApp
    public function getStatus()
    {
        try {
            $response = Http::timeout(5)->get("{$this->whatsappServiceUrl}/api/whatsapp/status");

            return response()->json($response->json());
        } catch (\Exception $e) {
            Log::error('Error obteniendo estado WhatsApp', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al conectar con servicio WhatsApp',
                'isConnected' => false
            ], 500);
        }
    }

    // Solicitar un codigo QR 
    public function pedirQR(Request $request)
    {
        try {
            $response = Http::timeout(10)->post("{$this->whatsappServiceUrl}/api/whatsapp/request-qr");

            return response()->json($response->json());
        } catch (\Exception $e) {
            Log::error('Error solicitando QR', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al solicitar QR'
            ], 500);
        }
    }

    // Resetear sesion de Whatsapp 
    public function resetSession(Request $request)
    {
        try {
            $response = Http::timeout(10)->post("{$this->whatsappServiceUrl}/api/whatsapp/reset");
            return response()->json($response->json());
        } catch (\Exception $e) {
            Log::error('Error reseteando sesión', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al resetear sesión'
            ], 500);
        }
    }

    // Enviar mensaje con imagen masiva a todos los leads de un producto
    public function enviarCampana(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
        ]);

        $productoId = $request->producto_id;

        $plantilla = WhatsappProducto::where('producto_id', $productoId)->first();

        if (!$plantilla) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró plantilla para este producto',
            ], 404);
        }

        $leads = Lead::where('product_id', $productoId)
                ->whereNotNull('phone')
                ->get();

        if ($leads->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No existen leads con teléfono para este producto'
            ], 422);
        }

        $exitosos = 0;
        $fallidos = 0;

        foreach ($leads as $lead) {
            $resultado = $this->enviarWhatsappALead($lead, $plantilla);
        
            if ($resultado['success']) {
                $exitosos++;
            } else {
                $fallidos++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Campaña de WhatsApp enviada',
            'total_leads' => $leads->count(),
            'exitosos' => $exitosos,
            'fallidos' => $fallidos
        ]);
            
    }

    private function enviarWhatsappALead($lead, $plantilla)
    {
        try {

            $mensaje = $plantilla->parrafo;
            $imagenUrl = $plantilla->imagen_principal;

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