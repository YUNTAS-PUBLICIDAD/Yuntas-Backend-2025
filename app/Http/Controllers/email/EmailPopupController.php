<?php
namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendProductEmailJob;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;

class EmailPopupController extends Controller
{
    public function enviar(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'message' => 'nullable|string',
            'product_id' => 'nullable|integer',
            'source_id' => 'required|integer',
        ]);

        if (!$request->product_id) { // SOLO POR EL MOMENTO, HASTA TENER PLANTILLA DE INICIO Y PRODUCTO
            return response()->json([
                'message' => 'Por ahora solo se envia email en detalle de producto'
            ], 200);
        }

        // Crear o actualizar lead
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

        $cliente = [
            'nombre' => $lead->name,
            'correo' => $lead->email,
            'telefono' => $lead->phone,
        ];

        $productoId = $lead->product_id;

        // Email inmediato (Día 0)
        SendProductEmailJob::dispatch($productoId, 0, $cliente);

        // Día 1
        SendProductEmailJob::dispatch($productoId, 1, $cliente)
            ->delay(now()->addDays(1));

        // Día 3
        SendProductEmailJob::dispatch($productoId, 2, $cliente)
            ->delay(now()->addDays(3));

        return response()->json([
            'message' => 'Secuencia de emails programada correctamente',
            'lead_id' => $lead->id,
            'total_correos_programados' => 3
        ], 200);
    }
}