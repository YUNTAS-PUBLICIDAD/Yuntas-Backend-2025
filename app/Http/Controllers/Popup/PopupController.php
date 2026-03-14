<?php

namespace App\Http\Controllers\Popup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Popup\StorePopupRequest;
use App\Http\Requests\Popup\UpdatePopupRequest;
use App\Models\Popup;
use App\Service\Image\ImageService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Str;

class PopupController extends Controller
{

  private ImageService $imageService;

  public function __construct(ImageService $imageService)
  {
    $this->imageService = $imageService;
  }

  /**
   * @OA\Get(
   *     path="/api/admin/popups",
   *     tags={"Popups"},
   *     summary="Obtener todos los popups",
   *     @OA\Response(response=200, description="Lista de popups")
   * )
   */
  public function index()
  {
    return Popup::orderBy('priority')->get();
  }

  public function show($id)
  {
    return Popup::findOrFail($id);
  }

  // Crear popup
  public function store(StorePopupRequest $request)
  {

    try {

      $data = $request->validated();
      $data['page_target'] = Str::slug($data['page_target']);

      if ($request->hasFile('image')) {
        $data['image'] = $this->imageService->store(
          $request->file('image'),
          'popups'
        );
      }

      $popup = Popup::create($data);

      return response()->json($popup, 201);

    } catch (Exception $e) {
      Log::error('Error al crear popup', [
        'error' => $e->getMessage()
      ]);

      return response()->json([
        'message' => 'Error al crear popup'
      ], 500);
    }
  }

  public function update(UpdatePopupRequest $request, $id)
  {

    try {
      $popup = Popup::findOrFail($id);

      $data = $request->validated();
      $data['page_target'] = Str::slug($data['page_target']);

      if ($request->hasFile('image')) {

        $data['image'] = $this->imageService->update(
          $request->file('image'),
          $popup->image,
          'popups'
        );
      }
      $popup->update($data);


      return response()->json($popup);

    } catch (Exception $e) {
      Log::error('Error al actualizar popup', [
        'error' => $e->getMessage()
      ]);

      return response()->json([
        'message' => 'Error al actualizar popup'
      ], 500);
    }

  }

  public function destroy($id)
  {
    try {
      $popup = Popup::findOrFail($id);

      if ($popup->image) {
        $this->imageService->remove($popup->image);
      }
      $popup->delete();

      return response()->json([
        'message' => 'Popup eliminado'
      ]);
    } catch (Exception $e) {
      Log::error('Error al eliminar popup', [
        'error' => $e->getMessage()
      ]);

      return response()->json([
        'message' => 'Error al eliminar popup'
      ], 500);
    }
  }

  // Endpoint público
  public function getPopup(Request $request)
  {
    $page = $request->query('page');
    if (! $page) {
      return response()->json(['message' => 'El parámetro "page" es obligatorio'], 400);
    }

    $popup = Popup::active()
      ->forPage($page)
      ->inSchedule()
      ->orderBy('priority')
      ->first();

    return response()->json($popup);
  }
}
