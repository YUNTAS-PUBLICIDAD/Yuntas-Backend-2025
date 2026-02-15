<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailProducto;
use App\Models\Lead;
use App\Models\EmailMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\ProductMailing1;

class EmailCampanaController extends Controller
{   
    public function __construct() {}

    // Envia un email de campaña a los leads asociados a un producto
    public function enviarCampana(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
        ]);

        $productoId = $request->producto_id;

        // Obtener plantillas del producto
        $secciones = EmailProducto::where('producto_id', $productoId)
            ->orderBy('paso')
            ->get();

        if ($secciones->isEmpty()) {
            Log::warning('No hay plantillas para el producto', [
                'producto_id' => $productoId,
            ]);

            return response()->json([
                'message' => 'No existe plantilla para este producto'
            ], 422);
        }

        $leads = Lead::where('product_id', $productoId)
            ->whereNotNull('email')
            ->get();

        if ($leads->isEmpty()) {
            Log::warning('No hay leads para el producto', [
                'producto_id' => $productoId,
            ]);

            return response()->json([
                'message' => 'No existen leads para este producto'
            ], 422);
        }

        // Envío de correos
        $campanasExitosas = 0;
        $campanasFallidas = 0;
    
        foreach ($leads as $lead) {
            $resultado = $this->enviarCorreosALead($lead, $secciones);

            if ($resultado['todos_exitosos']) {
                $campanasExitosas++;
            } else {
                $campanasFallidas++;
            }
        }

        return response()->json([
            'message' => 'Campaña enviada correctamente',
            'total_leads' => $leads->count(),
            'campanas_exitosas' => $campanasExitosas,
            'campanas_fallidas' => $campanasFallidas,
            'total_emails_enviados' => $leads->count() * $secciones->count()
        ]);
    }

    private function enviarCorreosALead($lead, $secciones)
    {
        // se genera un ID para la campaña
        $campaignId = Str::uuid()->toString();

        $cliente = [
            'nombre' => $lead->name,
            'correo' => $lead->email,
            'telefono' => $lead->phone,
        ];

        $todosExitosos = true;

        foreach ($secciones as $seccion) {
            try {
                Mail::to($lead->email)->send(
                    new ProductMailing1($seccion, $cliente)
                );

                // Guardar registro exitoso
                EmailMessage::create([
                    'lead_id' => $lead->id,
                    'type' => 'campaign',
                    'campaign_id' => $campaignId,
                    'subject' => $seccion->titulo,
                    'body' => $seccion->parrafo1,
                    'status' => 'enviado',
                    'sent_at' => now(),
                ]);
                
            } catch (\Exception $e) {
                $todosExitosos = false;

                Log::error('Error enviando email campaña', [
                    'lead_id' => $lead->id,
                    'campaign_id' => $campaignId,
                    'paso' => $seccion->paso,
                    'error' => $e->getMessage(),
                ]);

                // Guardar registro fallido
                EmailMessage::create([
                    'lead_id' => $lead->id,
                    'type' => 'campaign',
                    'campaign_id' => $campaignId,
                    'subject' => $seccion->titulo,
                    'body' => $seccion->parrafo1,
                    'status' => 'fallido',
                    'sent_at' => now(),
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        return ['todos_exitosos' => $todosExitosos, 'campaign_id' => $campaignId];
    }
}
