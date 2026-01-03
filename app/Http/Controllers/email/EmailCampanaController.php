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
        Log::info('🚀 Iniciando envío de campaña', $request->all());

        $request->validate([
            'producto_id' => 'required|integer',
        ]);

        $productoId = $request->producto_id;

        // 1️⃣ Obtener plantilla (secciones)
        $secciones = EmailProducto::where('producto_id', $productoId)
            ->orderBy('paso')
            ->get();

        Log::info('📧 Plantillas encontradas', [
            'producto_id' => $productoId,
            'total' => $secciones->count(),
        ]);

        if ($secciones->isEmpty()) {
            Log::warning('⚠️ No hay plantillas para el producto', [
                'producto_id' => $productoId,
            ]);

            return response()->json([
                'message' => 'No existe plantilla para este producto'
            ], 422);
        }

        // 2️⃣ Obtener leads del producto
        $leads = Lead::where('product_id', $productoId)
            ->whereNotNull('email')
            ->get();

        Log::info('👥 Leads encontrados', [
            'producto_id' => $productoId,
            'total' => $leads->count(),
        ]);

        if ($leads->isEmpty()) {
            Log::warning('⚠️ No hay leads para el producto', [
                'producto_id' => $productoId,
            ]);

            return response()->json([
                'message' => 'No existen leads para este producto'
            ], 422);
        }

        // 3️⃣ Envío de correos
        foreach ($leads as $lead) {

            $cliente = [
                'name'  => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
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

        Log::info('✅ Campaña finalizada correctamente', [
            'producto_id' => $productoId,
            'total_leads' => $leads->count(),
            'total_correos' => $leads->count() * $secciones->count(),
        ]);

        return response()->json([
            'message' => 'Campaña enviada correctamente',
            'total_leads' => $leads->count(),
            'total_correos' => $leads->count() * $secciones->count()
        ]);
    }
}
