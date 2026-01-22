<?php

namespace App\Http\Controllers\Email;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\CorreoPersonalizado;
use App\Http\Requests\EmailRequest;
use App\Mail\ProductInfoMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ProductEmailService;
use App\Service\Image\ImageService;
use App\Mail\ProductMailing1;
use App\Jobs\SendProductEmailJob;
use App\Models\EmailProducto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class EmailController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }


public function iniciarSeguimiento(Request $request)
{
    $request->validate([
        'nombre' => 'required|string',
        'telefono' => 'required|string',
        'correo' => 'required|email',
        'producto_id' => 'required|exists:products,id'
    ]);

    $cliente = $request->only(['nombre', 'telefono', 'correo']);
    $productoId = $request->producto_id;

    // 📧 Email inmediato (Día 0)
    SendProductEmailJob::dispatch($productoId, 0, $cliente);

    // 📧 Día 1
    SendProductEmailJob::dispatch($productoId, 1, $cliente)
        ->delay(now()->addDays(1));

    // 📧 Día 3
    SendProductEmailJob::dispatch($productoId, 2, $cliente)
        ->delay(now()->addDays(3));

    return response()->json([
        'message' => 'Secuencia de emails programada correctamente'
    ]);
}

private function obtenerPlantillaPorPaso($productoId, $paso)
{
    return EmailProducto::where('producto_id', $productoId)
        ->where('paso', $paso)
        ->first();
}

    public function sendEmail(EmailRequest $request)
    {
        $datosvalidados = $request->validated();

        try {
            Mail::to($datosvalidados['destinatario'])
                ->send(new CorreoPersonalizado([
                    'asunto' => $datosvalidados['asunto'],
                    'mensaje' => $datosvalidados['mensaje']
                ]));

            return response()->json(['message' => 'Correo enviado exitosamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al enviar el correo: ' . $e->getMessage()], 500);
        }
    }

}