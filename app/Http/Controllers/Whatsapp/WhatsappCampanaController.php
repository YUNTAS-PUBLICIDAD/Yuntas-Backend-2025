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
use App\Application\Services\CRM\LeadService;
use App\Application\DTOs\CRM\LeadDTO;

class WhatsappCampanaController extends Controller
{
    private $whatsappServiceUrl;

    public function __construct(
        private LeadService $leadService
    ) {
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

        // Actualizar o crear lead
        $lead = Lead::updateOrCreate(
            ['email' => $request->email], // Buscar por email
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'message' => $request->message,
                'product_id' => $request->product_id,
                'source_id' => $request->source_id,
            ]
        );

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
        $imagenProducto = null;
        if ($lead->product_id && $lead->source->name === 'Producto detalle') {
            $imagenProducto = $lead->product->images
                                    ->where('slot_id', 5) // Slot de imagen principal
                                    ->first()?->url;

            // variables adicionales
            $variables = array_merge($variables, [
                'producto_nombre' => $lead->product->name ?? '',
                'descripcion' => $lead->product->description ?? '',
                'fecha' => now()->format('d/m/Y'),
                'hora' => now()->format('H:i'),
                'email' => $lead->email,
            ]);
        }

        // Enviar mensaje al lead
        $resultado = $this->enviarWhatsappALead(
            $lead, 
            $plantillaPopup, 
            $variables,
            $imagenProducto
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

    // Enviar mensaje con imagen masiva a todos los leads de un producto
    public function enviarCampana(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
        ]);

        $productoId = $request->producto_id;

        if ($productoId === 0) { // plantilla por defecto, no se permite campaña masiva
             return response()->json([
                'success' => false,
                'message' => 'No se permite enviar campaña masiva con plantilla por defecto',
            ], 422);
        }

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

    private function enviarWhatsappALead($lead, $plantilla, array $variables = [], ?string $imagenOverride = null)
    {
        try {
            //  popup (WhatsappPopup)
            if ($plantilla instanceof WhatsappPopup) {
                $mensajeProcesado = $plantilla->procesarVariables($variables);
                $imagenUrl = $imagenOverride ?? $plantilla->imagen_url;
            } else {
                // campaña masiva (WhatsappProducto)
                $mensajeProcesado = $plantilla->parrafo;
                $imagenUrl = $plantilla->imagen_principal;
            }

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
                $payload['caption'] = $mensajeProcesado;

                Log::info('Enviando WhatsApp con imagen', [
                    'lead_id' => $lead->id,
                    'phone' => $payload['phone'],
                    'tiene_imagen' => true
                ]);

                $response = Http::timeout(30)->post("{$this->whatsappServiceUrl}/api/whatsapp/send-image", $payload);
            } else {
                $payload['message'] = $mensajeProcesado;

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
                'body' => $mensajeProcesado,
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
                'body' => $mensajeProcesado ?? '',
                'status' => 'fallido',
                'chat_id' => null,
                'image_url' => $imagenUrl ?? null,
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Obtener plantilla por defecto
    private function obtenerPlantillaDefault()
    {
        return WhatsappProducto::getDefault();
    }
}