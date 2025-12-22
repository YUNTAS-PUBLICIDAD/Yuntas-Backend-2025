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

    // Email inmediato
    SendProductEmailJob::dispatch($productoId, 0, $cliente);

    // +10 segundos
    SendProductEmailJob::dispatch($productoId, 1, $cliente)
        ->delay(now()->addMinutes(2));

    // +20 segundos
    SendProductEmailJob::dispatch($productoId, 2, $cliente)
        ->delay(now()->addMinutes(4));

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

    public function showByProducto($productoId)
    {
        $plantillas = EmailProducto::where('producto_id', $productoId)->get();

        if ($plantillas->isEmpty()) {
            return response()->json(['error' => 'No se encontraron plantillas para este producto'], 404);
        }

        // Transformar las plantillas al formato esperado por el frontend
        $secciones = $plantillas->map(function ($plantilla) {
            // Decodificar las imágenes secundarias si están en formato JSON
            $imagenesSecundarias = is_string($plantilla->imagenes_secundarias)
                ? json_decode($plantilla->imagenes_secundarias, true)
                : $plantilla->imagenes_secundarias;

            // Asegurar que sea un array
            $imagenesSecundarias = is_array($imagenesSecundarias) ? $imagenesSecundarias : [];

            return [
                'id' => $plantilla->id,
                'titulo' => $plantilla->titulo ?? '',
                'paso' => $plantilla->paso,
                'parrafo1' => $plantilla->parrafo1 ?? '',
                'imagen_principal_url' => $plantilla->imagen_principal
                    ? EmailProducto::buildImageUrl($plantilla->imagen_principal)
                    : null,
                'imagen_secundaria1_url' => isset($imagenesSecundarias[0])
                    ? EmailProducto::buildImageUrl($imagenesSecundarias[0])
                    : null,
                'imagen_secundaria2_url' => isset($imagenesSecundarias[1])
                    ? EmailProducto::buildImageUrl($imagenesSecundarias[1])
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'producto_id' => $productoId,
            'secciones' => $secciones
        ], 200);
    }

    //Crear plantilla email producto

    public function store(Request $request)
{
    $request->validate([
        'producto_id' => 'required|exists:products,id',  
        'titulo' => 'required|string|max:255',
        'parrafo1' => 'nullable|string',
        'imagen_principal' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
        'imagenes_secundarias.*' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
    ]);

    $paso = EmailProducto::where('producto_id', $request->producto_id)->count();
    $data = [
        'producto_id' => $request->producto_id,
        'paso' => $paso,
        'titulo' => $request->titulo,
        'parrafo1' => $request->parrafo1,
        'imagen_principal' => null,
        'imagenes_secundarias' => json_encode([]),
    ];

    // Imagen principal
    if ($request->hasFile('imagen_principal')) {
        $data['imagen_principal'] = $this->imageService->guardarImagen(
            $request->file('imagen_principal'),
            'email_productos'
        );
    }

    // Imágenes secundarias
    $secundarias = [];
    if ($request->hasFile('imagenes_secundarias')) {
        foreach ($request->file('imagenes_secundarias') as $img) {
            $secundarias[] = $this->imageService->guardarImagen($img, 'email_productos');
        }
    }
    $data['imagenes_secundarias'] = json_encode($secundarias);

    $created = EmailProducto::create($data);

    return response()->json([
        'message' => 'Plantilla creada correctamente',
        'data' => $created
    ]);

    // ------------------ VALIDACIÓN ------------------
    $validator = Validator::make($request->all(), [
        'producto_id' => 'required|exists:products,id',
        'secciones' => 'required|array|min:1',

        'secciones.*.titulo' => 'required|string|max:255',
        'secciones.*.paso' => 'required|integer|min:0',
        'secciones.*.parrafo1' => 'required|string',

        'secciones.*.imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        'secciones.*.imagenes_secundarias.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'trace_id' => $traceId,
            'errors' => $validator->errors()
        ], 422);
    }

    $productoId = $request->producto_id;
    $existe = EmailProducto::where('producto_id', $productoId)->exists();

    // si existe y no pidió replace => error
    $quiereReemplazar =
        $request->isMethod('put') ||
        $request->input('_method') === 'PUT' ||
        $request->query('replace') == 1 ||
        $request->input('replace') == 1;

    if ($existe && !$quiereReemplazar) {
        return response()->json([
            'trace_id' => $traceId,
            'error' => 'Ya existen emails para este producto. Se requiere replace=1.'
        ], 409);
    }

    $emailProductos = [];

    try {

        $resp = DB::transaction(function () use ($request, $productoId, $existe, $quiereReemplazar, &$emailProductos, $traceId, $log) {

            // SI EXISTE Y QUIERE REEMPLAZAR → ELIMINAR
            if ($existe && $quiereReemplazar) {
                $existentes = EmailProducto::where('producto_id', $productoId)->get();

                foreach ($existentes as $ep) {

                    if ($ep->imagen_principal) {
                        $this->imageService->eliminarImagen($ep->imagen_principal);
                    }

                    if ($ep->imagenes_secundarias) {
                        $arr = json_decode($ep->imagenes_secundarias, true) ?: [];
                        $this->imageService->eliminarImagenes($arr);
                    }

                    $ep->delete();
                }
            }

            // -------------------- CREAR NUEVAS SECCIONES --------------------
            foreach ($request->secciones as $index => $seccion) {

                $data = [
                    'producto_id' => $productoId,
                    'paso' => $index,
                    'titulo'      => $seccion['titulo'],
                    'parrafo1'    => $seccion['parrafo1'],
                    'imagen_principal' => null,
                    'imagenes_secundarias' => json_encode([]),
                ];

                EmailProducto::create($data);

                // IMAGEN PRINCIPAL
                if ($request->hasFile("secciones.$index.imagen_principal")) {

                    $img = $request->file("secciones.$index.imagen_principal");

                    if ($this->imageService->esImagenValida($img)) {

                        $ruta = $this->imageService->guardarImagen($img, 'email_productos');
                        $data['imagen_principal'] = $ruta;
                    }
                }

                // IMÁGENES SECUNDARIAS
                $secundarias = [];

                if ($request->hasFile("secciones.$index.imagenes_secundarias")) {

                    foreach ($request->file("secciones.$index.imagenes_secundarias") as $img2) {

                        if ($this->imageService->esImagenValida($img2)) {
                            $secundarias[] = $this->imageService->guardarImagen($img2, 'email_productos');
                        }
                    }
                }

                $data['imagenes_secundarias'] = json_encode($secundarias);

                // CREAR REGISTRO
                $created = EmailProducto::create($data);
                $emailProductos[] = $created;
            }

            return response()->json([
                'trace_id' => $traceId,
                'message' => $quiereReemplazar ? 'Reemplazo OK' : 'Creación OK',
                'data' => $emailProductos
            ], $quiereReemplazar ? 200 : 201);
        });

        return $resp;

    } catch (\Throwable $e) {

        foreach ($emailProductos as $ep) {
            if ($ep->imagen_principal) $this->imageService->eliminarImagen($ep->imagen_principal);

            if ($ep->imagenes_secundarias) {
                $arr = json_decode($ep->imagenes_secundarias, true) ?: [];
                $this->imageService->eliminarImagenes($arr);
            }

            $ep->delete();
        }

        return response()->json([
            'trace_id' => $traceId,
            'error' => 'Error interno al guardar la plantilla'
        ], 500);
    }
}

    //Actualizar plantilla email producto

    public function update(Request $request, $id)
    {
        $emailProducto = EmailProducto::find($id);

        if (!$emailProducto) {
            return response()->json(['error' => 'Email Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'producto_id' => 'sometimes|exists:products,id',
            'titulo' => 'sometimes|string|max:255',
            'paso' => 'sometimes|integer|min:0',
            'parrafo1' => 'sometimes|string',
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'imagenes_secundarias.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only(['producto_id', 'titulo', 'parrafo1']);

            // Actualizar imagen principal usando ImageService
            if ($request->hasFile('imagen_principal')) {
                $imagenPrincipal = $request->file('imagen_principal');

                if ($this->imageService->esImagenValida($imagenPrincipal)) {
                    $data['imagen_principal'] = $this->imageService->actualizarImagen(
                        $imagenPrincipal,
                        $emailProducto->imagen_principal,
                        'email_productos'
                    );
                }
            }

            // Actualizar imágenes secundarias usando ImageService
            if ($request->hasFile('imagenes_secundarias')) {
                // Eliminar imágenes anteriores
                if ($emailProducto->imagenes_secundarias) {
                    $imagenesAnteriores = json_decode($emailProducto->imagenes_secundarias, true);
                    if (is_array($imagenesAnteriores)) {
                        $this->imageService->eliminarImagenes($imagenesAnteriores);
                    }
                }

                // Guardar nuevas imágenes
                $imagenesSecundarias = [];
                foreach ($request->file('imagenes_secundarias') as $imagen) {
                    if ($this->imageService->esImagenValida($imagen)) {
                        $imagenesSecundarias[] = $this->imageService->guardarImagen(
                            $imagen,
                            'email_productos'
                        );
                    }
                }
                $data['imagenes_secundarias'] = json_encode($imagenesSecundarias);
            }

            $emailProducto->update($data);

            return response()->json([
                'message' => 'Email Producto actualizado exitosamente',
                'data' => $emailProducto
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el email: ' . $e->getMessage()
            ], 500);
        }
    }
}
