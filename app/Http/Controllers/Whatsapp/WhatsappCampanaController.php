<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\Lead;
use App\Models\WhatsappProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsappCampanaController extends Controller
{
    private $whatsappServiceUrl;

    public function __construct()
    {
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
    public function requestQR(Request $request)
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

    // Enviar mensaje de texto (falta mejorar)
    public function sendMessage(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
        ]);

        $productoId = $request->producto_id;

        $plantilla = WhatsappProducto::where('producto_id', $request->producto_id)->first();

        if (!$plantilla) {
            return response()->json([
                'message' => 'No se encontró plantilla para este producto',
            ], 404);
        }

        $leads = Lead::where('product_id', $productoId)
                ->whereNotNull('phone')
                ->get();

        if ($leads->isEmpty()) {
            return response()->json([
                'message' => 'No existen leads para este producto'
            ], 422);
        }

        try {
            foreach ($leads as $lead) {
                $response = Http::timeout(30)->post("{$this->whatsappServiceUrl}/api/whatsapp/send-message", [
                        'phone' => $lead->phone,
                        'message' => $plantilla->parrafo ?? ''
                ]);

                WhatsappMessage::create([
                    'lead_id' => $lead->id,
                    'body' => $plantilla->parrafo ?? '',
                    'status' => $response->json()['success'] ? 'sent' : 'failed',
                    'sent_at' => now(),
                    'error_message' => $response->json()['success'] ? null : $response->json()['message'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mensajes enviados correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando mensaje', [
                'phone' => $validated['phone'],
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar los mensajes'
            ], 500);
        }
    }

    // Enviar mensaje con imagen
    public function sendImage(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
        ]);

        $productoId = $request->producto_id;

        $plantilla = WhatsappProducto::where('producto_id', $request->producto_id)->first();

        if (!$plantilla) {
            return response()->json([
                'message' => 'No se encontró plantilla para este producto',
            ], 404);
        }

        $leads = Lead::where('product_id', $productoId)
                ->whereNotNull('phone')
                ->get();

        if ($leads->isEmpty()) {
            return response()->json([
                'message' => 'No existen leads para este producto'
            ], 422);
        }

        $image = Storage::disk('public')->get($plantilla->imagen_principal);
        $imageData = base64_encode($image);

        try {
            foreach ($leads as $lead) {
                $response = Http::timeout(30)->post("{$this->whatsappServiceUrl}/api/whatsapp/send-image", [
                        'phone' => $lead->phone,
                        'imageData' => $imageData,
                        'caption' => $plantilla->parrafo ?? ''
                ]);

                WhatsappMessage::create([
                    'lead_id' => $lead->id,
                    'body' => $plantilla->parrafo ?? '',
                    'status' => $response->json()['success'] ? 'sent' : 'failed',
                    'image_url' => $plantilla->imagen_principal,
                    'sent_at' => now(),
                    'error_message' => $response->json()['success'] ? null : $response->json()['message'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mensajes enviados correctamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error enviando imagen', [
                'phone' => $validated['phone'],
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar los mensajes'
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
}