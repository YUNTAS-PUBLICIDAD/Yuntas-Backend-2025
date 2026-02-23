<?php
namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\EmailProducto;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Traits\ValidatesImageSecurity;

class EmailProductController extends Controller
{
    use ValidatesImageSecurity;

    // OBTENER PLANTILLAS POR PRODUCTO
    public function indexByProduct(Request $request)
    {
        $query = EmailProducto::query();

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        return $query->orderBy('paso')->get();
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

            // Valida la imagen principal
            try {
                $this->validateImageSecurity($request->file('imagen_principal'));
            } catch (\InvalidArgumentException $e) {
                return response()->json([
                    'message' => 'Imagen principal no válida: ',
                    'errors' => [
                        'imagen_principal' => [$e->getMessage()]
                    ]
                ], 422);
            }
            // Eliminar imagen anterior si existe
            if ($plantilla && $plantilla->imagen_principal) {
                $oldPath = str_replace('storage/', '', $plantilla->imagen_principal);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('imagen_principal')->store('uploads/email', 'public');
            $data['imagen_principal'] = 'storage/' . $path;
        } elseif ($plantilla) {
            // mantener la existente
            $data['imagen_principal'] = $plantilla->imagen_principal;
        }

        // IMÁGENES SECUNDARIAS
        if ($request->hasFile('imagenes_secundarias')) {

            // Validar cada imagen secundaria
            foreach ($request->file('imagenes_secundarias') as $index => $img) {
                try {
                    $this->validateImageSecurity($img);
                } catch (\InvalidArgumentException $e) {
                    return response()->json([
                        'message' => "Imagen secundaria #" . ($index + 1) . " no válida",
                        'errors' => [
                            "imagenes_secundarias.{$index}" => [$e->getMessage()]
                        ]
                    ], 422);
                }
            }

            if ($plantilla && $plantilla->imagenes_secundarias) { // eliminar las anteriores si existen
                $oldImages = json_decode($plantilla->imagenes_secundarias, true) ?? [];
                foreach ($oldImages as $oldImage) {
                    $oldPath = str_replace('storage/', '', $oldImage);
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $imagenes = []; // REEMPLAZA, no acumula
            foreach ($request->file('imagenes_secundarias') as $img) {
                $path = $img->store('uploads/email', 'public');
                $imagenes[] = 'storage/' . $path;
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
        ]);
    }
    
    // ELIMINAR PLANTILLA
    public function destroy(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:products,id',
            'paso' => 'required|integer|min:0|max:2',
        ]);

        $plantilla = EmailProducto::where('producto_id', $request->producto_id)
            ->where('paso', $request->paso)
            ->first();

        if (!$plantilla) {
            return response()->json([
                'message' => 'Plantilla no encontrada'
            ], 404);
        }

        // Eliminar imagen principal
        if ($plantilla->imagen_principal) {
            $oldPath = str_replace('storage/', '', $plantilla->imagen_principal);
            Storage::disk('public')->delete($oldPath);
        }

        // Eliminar imágenes secundarias
        if ($plantilla->imagenes_secundarias) {
            $oldImages = json_decode($plantilla->imagenes_secundarias, true) ?? [];
            foreach ($oldImages as $oldImage) {
                $oldPath = str_replace('storage/', '', $oldImage);
                Storage::disk('public')->delete($oldPath);
            }
        }

        $plantilla->delete();

        return response()->json([
            'message' => 'Plantilla eliminada correctamente'
        ]);
    }
}
