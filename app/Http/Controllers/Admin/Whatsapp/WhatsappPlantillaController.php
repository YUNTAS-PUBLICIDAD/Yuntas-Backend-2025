<?php

namespace App\Http\Controllers\Admin\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappPlantilla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WhatsappPlantillaController extends Controller
{
    // Obtener la plantilla (GET)
    public function index(Request $request)
    {
        
        $productoId = $request->query('producto_id');

        if (!$productoId) {
            return response()->json(['success' => false, 'message' => 'ID de producto requerido'], 400);
        }

        $plantilla = WhatsappPlantilla::where('producto_id', $productoId)->first();

        return response()->json([
            'success' => true,
            'data' => $plantilla 
        ]);
    }

    // Guardar o Actualizar (POST)
    public function store(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|integer',
            'parrafo' => 'required|string',
            'imagen_principal' => 'nullable' 
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        
        $plantilla = WhatsappPlantilla::where('producto_id', $request->producto_id)->first();

      
        $rutaImagen = $plantilla ? $plantilla->imagen_principal : null;

        if ($request->hasFile('imagen_principal')) {
            // Si suben una nueva imagen, borramos la anterior si existe
            if ($plantilla && $plantilla->imagen_principal) {
                Storage::disk('public')->delete($plantilla->imagen_principal);
            }
            
            $rutaImagen = $request->file('imagen_principal')->store('whatsapp', 'public');
        }

        
        $plantilla = WhatsappPlantilla::updateOrCreate(
            ['producto_id' => $request->producto_id], 
            [
                'parrafo' => $request->parrafo,
                'imagen_principal' => $rutaImagen
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Plantilla guardada correctamente',
            'data' => $plantilla
        ]);
    }
}