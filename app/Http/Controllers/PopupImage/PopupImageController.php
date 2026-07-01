<?php

namespace App\Http\Controllers\PopupImage;

use App\Http\Controllers\Controller;
use App\Models\PopupImage;
use App\Service\Image\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class PopupImageController extends Controller
{
  /**
 * @OA\Patch(
 *     path="/api/admin/popup-images/{id}",
 *     tags={"PopupImages"},
 *     summary="Actualizar una imagen de popup (parcial)",
 *     description="Permite actualizar imagen, device, slot, alt o title de forma independiente",
 *     operationId="updatePopupImage",
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la imagen",
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *
 *                 @OA\Property(
 *                     property="file",
 *                     type="string",
 *                     format="binary",
 *                     description="Nueva imagen"
 *                 ),
 *                 @OA\Property(
 *                     property="device",
 *                     type="string",
 *                     enum={"desktop","mobile"}
 *                 ),
 *                 @OA\Property(
 *                     property="slot",
 *                     type="string",
 *                     enum={"left","right","center"}
 *                 ),
 *                 @OA\Property(
 *                     property="alt",
 *                     type="string",
 *                     nullable=true
 *                 ),
 *                 @OA\Property(
 *                     property="title",
 *                     type="string",
 *                     nullable=true
 *                 ),
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Imagen actualizada correctamente"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación o reglas de negocio"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Imagen no encontrada"
 *     )
 * )
 */
  public function update(Request $request, $id, ImageService $imageService)
  {
    DB::beginTransaction();

    try {
      $image = PopupImage::findOrFail($id);

      // Validación flexible (PATCH real)

      // Validación simple
      $request->validate([
        // 'file' => 'sometimes|image|max:2048',
        'file' => [
          'sometimes',
          'file',
          'image',
          'mimes:webp',
          'max:2048'
        ],
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
        Log::info('Actualizando imagen', [
        'old_path' => $oldPath
        ]);

        $newPath = $imageService->store(
          $request->file('file'),
          'popups'
        );

        Log::info('Nueva imagen guardada', [
        'new_path' => $newPath
        ]);

        $data['image'] = $newPath;

        try {

        // Eliminar después de guardar (seguro)
        $imageService->remove($oldPath);
        Log::info('Imagen anterior eliminada');
        } catch (Throwable $e) {
          Log::warning('Error eliminando imagen anterior', [
          'error' => $e->getMessage()
          ]);
        }

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

      Log::info('REQUEST DATA', $request->all());
      Log::info('FILES', $request->allFiles());

       Log::error('PopupImage update error', [
    'id' => $id,
    'error' => $th->getMessage(),
    'trace' => $th->getTraceAsString()
  ]);

      return response()->json([
        'message'  => 'Error al actualizar imagen'
      ], 500);
    }
  }

}
