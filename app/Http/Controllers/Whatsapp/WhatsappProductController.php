<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class WhatsappProductController extends Controller
{
    // OBTENER PLANTILLAS POR PRODUCTO
    public function indexByProduct(Request $request) {
        $request->validate([
            'producto_id' => 'required|integer|exists:products,id'
        ]);

        $plantilla = WhatsappProducto::where('producto_id', $request->producto_id)->first();

        if (!$plantilla) {
            return response()->json([
                'message' => 'No se encontró plantilla para este producto',
                'data' => null
            ], 200);
        }

        return response()->json([
            'message' => 'Plantilla encontrada',
            'data' => $plantilla
        ]);
    }

    // CREAR O ACTUALIZAR
    public function store(Request $request) {
        $request->validate([
            'producto_id' => 'required|integer|exists:products,id',
        ]);

        $plantilla = WhatsappProducto::where('producto_id', $request->producto_id)->first();

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
            'parrafo'    => $request->parrafo,
            'is_default' => false,
        ];

        // IMAGEN PRINCIPAL
        if ($request->hasFile('imagen_principal')) {
            // Eliminar imagen anterior si existe
            if ($plantilla && $plantilla->imagen_principal) {
                $oldPath = str_replace('storage/', '', $plantilla->imagen_principal);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('imagen_principal')->store('uploads/whatsapp', 'public');
            $data['imagen_principal'] = 'storage/' . $path;
        } elseif ($plantilla) {
            // mantener la existente
            $data['imagen_principal'] = $plantilla->imagen_principal;
        }

        // GUARDAR O ACTUALIZAR
        $saved = WhatsappProducto::updateOrCreate(
            ['producto_id' => $request->producto_id,],
            $data
        );

        $accion = $plantilla ? 'actualizada' : 'creada';

        return response()->json([
            'message' => "Plantilla {$accion} correctamente",
            'data' => $saved,
        ]);
    }

    // OBTENER PLANTILLA POR DEFECTO
    public function getDefault() {
        $plantilla = WhatsappProducto::getDefault();

        if (!$plantilla) {
            return response()->json([
                'message' => 'No se encontró plantilla por defecto',
                'data' => null
            ], 200);
        }

        return response()->json([
            'message' => 'Plantilla por defecto encontrada',
            'data' => $plantilla
        ]);
    }

    // CREAR O ACTUALIZAR PLANTILLA POR DEFECTO
    public function storeDefault(Request $request) {
        $plantilla = WhatsappProducto::getDefault();

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
            'producto_id' => null, // Sin producto
            'parrafo'    => $request->parrafo,
            'is_default' => true,
        ];

        // IMAGEN PRINCIPAL
        if ($request->hasFile('imagen_principal')) {
            // Eliminar imagen anterior si existe
            if ($plantilla && $plantilla->imagen_principal) {
                $oldPath = str_replace('storage/', '', $plantilla->imagen_principal);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('imagen_principal')->store('uploads/whatsapp', 'public');
            $data['imagen_principal'] = 'storage/' . $path;
        } elseif ($plantilla) {
            // mantener la existente
            $data['imagen_principal'] = $plantilla->imagen_principal;
        }

        // GUARDAR O ACTUALIZAR
        if ($plantilla) {
            // Actualizar existente
            $plantilla->update($data);
            $saved = $plantilla;
            $accion = 'actualizada';
        } else {
            // Crear nueva
            $saved = WhatsappProducto::create($data);
            $accion = 'creada';
        }

        return response()->json([
            'message' => "Plantilla por defecto {$accion} correctamente",
            'data' => $saved,
        ]);
    }
}
