<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\EmailProducto;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;


class EmailProductController extends Controller
{
    // LISTAR
    public function index(Request $request)
{
    $query = EmailProducto::query();

    if ($request->filled('producto_id')) {
        $query->where('producto_id', $request->producto_id);
    }

    return $query->orderBy('paso')->get();
}

    // OBTENER PLANTILLAS POR PRODUCTO
   public function show($id)
{
    return EmailProducto::findOrFail($id);
}
    

    // CREAR
    public function store(Request $request)
{
    $request->validate([
        'producto_id' => 'required|integer|exists:products,id',
        'paso' => 'required|integer|min:0|max:2',
        'titulo' => 'required|string|max:250',
        'parrafo1' => 'nullable|string|max:250',

        'imagen_principal' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        'imagenes_secundarias.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    // Buscar si ya existe esa plantilla (producto + paso)
    $plantilla = EmailProducto::where('producto_id', $request->producto_id)
        ->where('paso', $request->paso)
        ->first();

    // se valida la imagen principal cuando es nueva
    if (!$plantilla && !$request->hasFile('imagen_principal')) {
        return response()->json([
            'message' => 'La imagen principal es obligatoria para crear una nueva plantilla',
            'errors' => [
                'imagen_principal' => ['Se requiere una imagen principal']
            ]
        ], 422);
    }

    $data = [
        'producto_id' => $request->producto_id,
        'paso'        => $request->paso,
        'titulo'      => $request->titulo,
        'parrafo1'    => $request->parrafo1,
    ];

    // IMAGEN PRINCIPAL
    if ($request->hasFile('imagen_principal')) {
        // Eliminar imagen anterior si existe
        if ($plantilla && $plantilla->imagen_principal) {
            $oldPath = str_replace(asset('storage/'), '', $plantilla->imagen_principal);
            Storage::disk('public')->delete($oldPath);
        }
        
        $path = $request->file('imagen_principal')->store('uploads/email', 'public');
        $data['imagen_principal'] = asset('storage/' . $path);
    } elseif ($plantilla) {
        // mantener la existente
        $data['imagen_principal'] = $plantilla->imagen_principal;
    }

    // IMÁGENES SECUNDARIAS
    if ($request->hasFile('imagenes_secundarias')) {
        if ($plantilla && $plantilla->imagenes_secundarias) { // eliminar las anteriores si existen
            $oldImages = json_decode($plantilla->imagenes_secundarias, true) ?? [];
            foreach ($oldImages as $oldImage) {
                $oldPath = str_replace(asset('storage/'), '', $oldImage);
                Storage::disk('public')->delete($oldPath);
            }
        }
        $imagenes = []; // REEMPLAZA, no acumula
        foreach ($request->file('imagenes_secundarias') as $img) {
            $path = $img->store('uploads/email', 'public');
            $imagenes[] = asset('storage/' . $path);
        }

        $data['imagenes_secundarias'] = json_encode($imagenes);

    } elseif ($plantilla) {
        // Mantener las existentes
        $data['imagenes_secundarias'] = $plantilla->imagenes_secundarias;
    } else {
        // Nueva plantilla sin secundarias
        $data['imagenes_secundarias'] = json_encode([]);
    }

    // GUARDAR O ACTUALIZAR
    $saved = EmailProducto::updateOrCreate(
        [
            'producto_id' => $request->producto_id,
            'paso'        => $request->paso,
        ],
        $data
    );

    $accion = $plantilla ? 'actualizada' : 'creada';

    return response()->json([
        'message' => "Plantilla {$accion} correctamente",
        'data' => $saved,
        'es_nueva' => !$plantilla
    ]);
}

    // ACTUALIZAR
    public function update(Request $request, $id)
    {
        $email = EmailProducto::findOrFail($id);

        $request->validate([
            
            'titulo' => 'nullable|string',
            'parrafo1' => 'nullable|string',

            // ARCHIVOS
            'imagen_principal' => 'nullable|image|mimes:jpg,jpeg,png',
            'imagenes_secundarias.*' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $data = $request->except(['imagenes_secundarias', 'imagen_principal']);

        if ($request->hasFile('imagen_principal')) {
            $file = $request->file('imagen_principal');
            $path = $file->store('uploads/email', 'public');
            $data['imagen_principal'] = asset('storage/' . $path);
        }

        $imagenes = json_decode($email->imagenes_secundarias, true) ?? [];

        if ($request->hasFile('imagenes_secundarias')) {
            foreach ($request->file('imagenes_secundarias') as $img) {
                $path = $img->store('uploads/email', 'public');
                $imagenes[] = asset('storage/' . $path);
            }
        }

        $data['imagenes_secundarias'] = json_encode($imagenes);

        $email->update($data);

        return response()->json([
            "message" => "Plantilla actualizada correctamente",
            "data" => $email
        ]);
    }
}
