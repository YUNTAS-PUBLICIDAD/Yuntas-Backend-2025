<?php
namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendProductEmailJob;
use Illuminate\Support\Facades\Mail;
use App\Mail\InicioMailing;
use App\Mail\ProductosMailing;
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


        // enviar si viene de inicio o producto
        if (!$lead->product_id) {
            if ($lead->source->name === 'Inicio') {
                try {
                    Mail::to($cliente['correo'])->send(new InicioMailing($cliente));

                    // Guardar registro
                    EmailMessage::create([
                        'lead_id' => $lead->id,
                        'type' => 'popup',
                        'subject' => 'Bienvenido a Yuntas',
                        'body' => 'Email de bienvenida desde Inicio',
                        'status' => 'enviado',
                        'sent_at' => now(),
                    ]);

                    return response()->json([
                        'message' => 'Email de Inicio enviado correctamente',
                        'lead_id' => $lead->id,
                    ], 200);
                } catch (\Exception $e) {
                    EmailMessage::create([
                        'lead_id' => $lead->id,
                        'type' => 'popup',
                        'subject' => 'Bienvenido a Yuntas',
                        'body' => 'Email de bienvenida desde Inicio',
                        'status' => 'fallido',
                        'sent_at' => now(),
                        'error_message' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            } else if ($lead->source->name === 'Productos') {
                try {
                    Mail::to($cliente['correo'])->send(new ProductosMailing($cliente));

                    EmailMessage::create([
                        'lead_id' => $lead->id,
                        'type' => 'popup',
                        'subject' => 'Bienvenido a Yuntas',
                        'body' => 'Email de productos',
                        'status' => 'enviado',
                        'sent_at' => now(),
                    ]);

                    return response()->json([
                        'message' => 'Email de Productos enviado correctamente',
                        'lead_id' => $lead->id,
                    ], 200);
                } catch (\Exception $e) {
                    EmailMessage::create([
                        'lead_id' => $lead->id,
                        'type' => 'popup',
                        'subject' => 'Bienvenido a Yuntas',
                        'body' => 'Email de productos',
                        'status' => 'fallido',
                        'sent_at' => now(),
                        'error_message' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            } else {
                Log::info('Lead no viene de Inicio ni Productos, no se envía email', [
                    'lead_id' => $lead->id,
                    'source' => $lead->source->name,
                ]);
                return response()->json([
                    'message' => 'Lead registrado sin envío de email',
                    'lead_id' => $lead->id,
                ], 200);
            }
        }

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