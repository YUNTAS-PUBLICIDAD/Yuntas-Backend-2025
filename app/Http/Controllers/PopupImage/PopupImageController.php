<?php

namespace App\Http\Controllers\PopupImage;

use App\Http\Controllers\Controller;
use App\Models\PopupImage;
use App\Service\Image\ImageService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PopupImageController extends Controller
{
  public function update(Request $request, $id, ImageService $imageService)
  {
    DB::beginTransaction();

    try {
      $image = PopupImage::findOrFail($id);

      // Validación flexible (PATCH real)

      // Validación simple
      $request->validate([
        'file' => 'sometimes|image|max:2048',
        'device' => ['sometimes', Rule::in(['desktop', 'mobile'])],
        'slot' => ['sometimes', Rule::in(['left', 'right', 'center'])],
        'alt' => 'nullable|string|max:255',
        'title' => 'nullable|string|max:255',
      ]);

      // Validación de negocio
      $newDevice = $request->device ?? $image->device;
      $popupId = $image->popup_id;

      $count = PopupImage::where('popup_id', $popupId)
      ->where('device', $newDevice)
      ->where('id', '!=', $image->id)
      ->count();

      if ($newDevice === 'desktop' && $count >= 2) {
        return response()->json([
          'message' => 'Solo ser permiten 2 imágenes desktop'
        ], 422);
      }

      if ($newDevice === 'mobile' && $count >= 1) {
        return response()->json([
          'message' => 'Solo se permite 1 imagen mobile'
        ], 422);
      }

      // Update parcial
      $data = [];

      // Imagen si viene
      if ($request->hasFile('file')) {
        $oldPath = $image->image;

        $data['image'] = $imageService->store(
          $request->file('file'),
          'popups'
        );

        // Eliminar después de guardar (seguro)
        $imageService->remove($oldPath); 
      }
      if ($request->has('device')) {
        $data['device'] = $request->device;
      }

      if ($request->has('slot')) {
        $data['slot'] = $request->slot;
      }

      if ($request->exists('alt')) {
        $data['alt'] = $request->alt;
      }

      if ($request->exists('title')) {
        $data['title'] = $request->title;
      }

      $image->update($data);
      DB::commit();
      return response()->json($image);
    } catch (\Throwable $th) {
      DB::rollBack();

       \Log::error('PopupImage update error', [
    'id' => $id,
    'error' => $th->getMessage()
  ]);
      
      return response()->json([
        'message'  => 'Error al actualizar imagen'
      ], 500);
    }
  }

}
