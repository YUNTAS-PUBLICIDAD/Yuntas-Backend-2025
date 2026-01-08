<?php
# solucionando el nombre de la carpeta Email con mayúscula
namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\EmailProducto;
use Illuminate\Http\Request;

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
        'producto_id' => 'required|integer',
        'paso' => 'required|integer|min:0',
        'titulo' => 'required|string',
        'parrafo1' => 'nullable|string',

        'imagen_principal' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        'imagenes_secundarias.*' => 'nullable|image|mimes:jpg,jpeg,png,webp',
    ]);

    // Buscar si ya existe esa plantilla (producto + paso)
    $email = EmailProducto::where('producto_id', $request->producto_id)
        ->where('paso', $request->paso)
        ->first();

    $data = [
        'producto_id' => $request->producto_id,
        'paso'        => $request->paso,
        'titulo'      => $request->titulo,
        'parrafo1'    => $request->parrafo1,
    ];

    // ==============================
    // IMAGEN PRINCIPAL
    // ==============================
    if ($request->hasFile('imagen_principal')) {
        $path = $request->file('imagen_principal')
            ->store('uploads/email', 'public');

        $data['imagen_principal'] = asset('storage/' . $path);
    } elseif ($email) {
        // mantener la existente
        $data['imagen_principal'] = $email->imagen_principal;
    }

    // ==============================
    // IMÁGENES SECUNDARIAS
    // ==============================
    $imagenes = $email
        ? json_decode($email->imagenes_secundarias, true) ?? []
        : [];

    if ($request->hasFile('imagenes_secundarias')) {
        $imagenes = []; // REEMPLAZA, no acumula
        foreach ($request->file('imagenes_secundarias') as $img) {
            $path = $img->store('uploads/email', 'public');
            $imagenes[] = asset('storage/' . $path);
        }
    }

    $data['imagenes_secundarias'] = json_encode($imagenes);

    // ==============================
    // UPDATE O CREATE (ANTI DUPLICADO)
    // ==============================
    $saved = EmailProducto::updateOrCreate(
        [
            'producto_id' => $request->producto_id,
            'paso'        => $request->paso,
        ],
        $data
    );

    return response()->json([
        'message' => 'Plantilla guardada correctamente',
        'data' => $saved
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
