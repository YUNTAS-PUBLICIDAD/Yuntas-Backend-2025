<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailProducto;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ProductMailing1;

class EmailCampanaController extends Controller
{
    public function enviar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
        ]);

        $productoId = $request->producto_id;

        $secciones = EmailProducto::where('producto_id', $productoId)
            ->orderBy('paso')
            ->get();

        if ($secciones->isEmpty()) {
            Log::warning('⚠️ No hay plantillas para el producto', [
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
            Log::warning('⚠️ No hay leads para el producto', [
                'producto_id' => $productoId,
            ]);

            return response()->json([
                'message' => 'No existen leads para este producto'
            ], 422);
        }

        // Envío de correos
        foreach ($leads as $lead) {

            $cliente = [
                'nombre' => $lead->name,
                'correo' => $lead->email,
                'telefono' => $lead->phone,
            ];

            Log::info('✉️ Enviando correos a lead', [
                'email' => $lead->email,
                'nombre' => $lead->name,
            ]);

            foreach ($secciones as $seccion) {
                Log::info('➡️ Enviando sección', [
                    'email' => $lead->email,
                    'paso' => $seccion->paso,
                    'titulo' => $seccion->titulo,
                ]);

                Mail::to($lead->email)->send(
                    new ProductMailing1($seccion, $cliente)
                );
            }
        }

        return response()->json([
            'message' => 'Campaña enviada correctamente',
            'total_leads' => $leads->count(),
            'total_correos' => $leads->count() * $secciones->count()
        ]);
    }
}
