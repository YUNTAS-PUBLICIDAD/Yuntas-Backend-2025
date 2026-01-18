<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\Lead;
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
            'source_id' => 'nullable|integer',
        ]);

        $productoId = $request->product_id;

        // Buscar o crear el lead
        $lead = Lead::where('email', $request->email)->first();

        if (!$lead) {
            // Crear nuevo lead
            $lead = Lead::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message ?? null,
                'product_id' => $request->product_id ?? null,
                'source_id' => $request->source_id ?? null,
            ]);
        }

        // Validar que tenga teléfono
        if (!$lead->phone) {
            return response()->json([
                'success' => false,
                'message' => 'El lead no tiene número de teléfono'
            ], 422);
        }

        if ($productoId) {
            // Obtener plantilla del producto
            $plantilla = WhatsappProducto::where('producto_id', $productoId)->first();
            if (!$plantilla) {
                $plantilla = $this->obtenerPlantillaDefault();
            }
        } else {
            $plantilla = $this->obtenerPlantillaDefault();
        }

        if (!$plantilla) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró plantilla para enviar',
            ], 404);
        }

        // Enviar mensaje al lead
        $resultado = $this->enviarWhatsappALead($lead, $plantilla);

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

    private function enviarWhatsappALead($lead, $plantilla)
    {
        try {
            // Si tiene imagen, enviar con imagen
            if ($plantilla->imagen_principal) {
                $imagePath = str_replace('storage/', '', $plantilla->imagen_principal);

                $image = Storage::disk('public')->get($imagePath);
                $imageData = base64_encode($image);

                $response = Http::timeout(30)->post("{$this->whatsappServiceUrl}/api/whatsapp/send-image", [
                    'phone' => strlen($lead->phone) === 9 ? '51' . $lead->phone : $lead->phone,
                    'imageData' => $imageData,
                    'caption' => $plantilla->parrafo ?? ''
                ]);
            } else {
                // Enviar solo texto
                $response = Http::timeout(30)->post("{$this->whatsappServiceUrl}/api/whatsapp/send-message", [
                    'phone' => strlen($lead->phone) === 9 ? '51' . $lead->phone : $lead->phone,
                    'message' => $plantilla->parrafo ?? ''
                ]);
            }

            $success = $response->json()['success'] ?? false;

            // Guardar registro del mensaje
            WhatsappMessage::create([
                'lead_id' => $lead->id,
                'body' => $plantilla->parrafo ?? '',
                'status' => $success ? 'enviado' : 'fallido',
                'image_url' => $plantilla->imagen_principal ?? null,
                'sent_at' => now(),
                'error_message' => $success ? null : ($response->json()['message'] ?? 'Error desconocido'),
            ]);

            return ['success' => $success];

        } catch (\Exception $e) {
            // Guardar registro del error
            WhatsappMessage::create([
                'lead_id' => $lead->id,
                'body' => $plantilla->parrafo ?? '',
                'status' => 'fallido',
                'image_url' => $plantilla->imagen_principal ?? null,
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