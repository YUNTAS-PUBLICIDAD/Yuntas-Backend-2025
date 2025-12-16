<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\EmailProducto;
use Illuminate\Http\Request;

class EmailProductController extends Controller
{
    // LISTAR
    public function index()
    {
        return EmailProducto::all();
    }

    // CREAR
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
            'paso' => 'required|integer',
            'titulo' => 'required|string',
            'parrafo1' => 'required|string',

            // ARCHIVOS
            'imagen_principal' => 'nullable|image|mimes:jpg,jpeg,png',
            'imagenes_secundarias.*' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $data = $request->except(['imagenes_secundarias', 'imagen_principal']);

        // ==============================
        // GUARDAR IMAGEN PRINCIPAL
        // ==============================
        
        if ($request->hasFile('imagen_principal')) {
            $file = $request->file('imagen_principal');
            $path = $file->store('uploads/email', 'public');
            $data['imagen_principal'] = "/storage/" . $path;
        }

        // ==============================
        // GUARDAR IMÁGENES SECUNDARIAS
        // ==============================
        $imagenes = [];

        if ($request->hasFile('imagenes_secundarias')) {
            foreach ($request->file('imagenes_secundarias') as $img) {
                $path = $img->store('uploads/email', 'public');
                $imagenes[] = "/storage/" . $path;
            }
        }

        $data['imagenes_secundarias'] = json_encode($imagenes);

        $email = EmailProducto::create($data);

        return response()->json([
            "message" => "Plantilla creada correctamente",
            "data" => $email
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
            $data['imagen_principal'] = "/storage/" . $path;
        }

        $imagenes = json_decode($email->imagenes_secundarias, true) ?? [];

        if ($request->hasFile('imagenes_secundarias')) {
            foreach ($request->file('imagenes_secundarias') as $img) {
                $path = $img->store('uploads/email', 'public');
                $imagenes[] = "/storage/" . $path;
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
