<?php

namespace App\Http\Controllers\Popup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Popup\StorePopupRequest;
use App\Http\Requests\Popup\UpdatePopupRequest;
use App\Models\Popup;
use App\Models\PopupImage;
use App\Service\Image\ImageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
    // return Popup::orderBy('priority')->get();
    return Popup::with('images')
    ->orderBy('priority')
    ->get();
  }

  /**
 * @OA\Get(
 *     path="/api/admin/popups/{id}",
 *     tags={"Popups"},
 *     summary="Obtener popup por ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del popup",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Popup encontrado"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Popup no encontrado"
 *     )
 * )
 */
  public function show($id)
  {
    // return Popup::findOrFail($id);
    return Popup::with('images')->findOrFail($id);
  }

  /**
 * @OA\Post(
 *     path="/api/admin/popups",
 *     tags={"Popups"},
 *     summary="Crear popup",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"lead_source_id","title","button_text","page_target","delay_seconds","priority","images"},
 *
 *                 @OA\Property(property="lead_source_id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Popup de prueba"),
 *                 @OA\Property(property="button_text", type="string", example="Aceptar"),
 *                 @OA\Property(property="button_color", type="string", example="#f54927"),
 *                 @OA\Property(property="page_target", type="string", example="inicio"),
 *                 @OA\Property(property="delay_seconds", type="integer", example=5),
 *                 @OA\Property(property="priority", type="integer", example=1),
 *                 @OA\Property(property="active", type="boolean", example=true),

 *                 @OA\Property(
 *                     property="images",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         required={"file","device","slot"},
 *
 *                         @OA\Property(
 *                             property="file",
 *                             type="string",
 *                             format="binary"
 *                         ),
 *                         @OA\Property(
 *                             property="device",
 *                             type="string",
 *                             enum={"desktop","mobile"},
 *                             example="desktop"
 *                         ),
 *                         @OA\Property(
 *                             property="slot",
 *                             type="string",
 *                             enum={"left","right","center"},
 *                             example="left"
 *                         ),
 *                         @OA\Property(property="alt", type="string", example="Imagen izquierda"),
 *                         @OA\Property(property="title", type="string", example="Promo izquierda")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Popup creado correctamente"),
 *     @OA\Response(response=422, description="Error de validación")
 * )
 */

  // Crear popup
  public function store(StorePopupRequest $request)
  {
    DB::beginTransaction();
    try {
      Log::info('STORE POPUP - inicio');

     Log::info('DEBUG PRODUCT_ID INPUT', [
      'product_id' => $request->input('product_id'),
      'has_product_id' => $request->has('product_id'),
      'all' => $request->all(),
     ]);

      Log::info('Request data', [
        'all' => $request->all(),
        'files' => $request->files->all()
      ]);

      Log::info('RAW REQUEST', $request->all());
      Log::info('PAGE TARGET', [$request->input('page_target')]);
      Log::info('PRODUCT ID', [$request->input('product_id')]);
// dd($request->all(), $request->file('images'));
      $data = $request->validated();

      Log::info('Data validada', $data);

      $data = $request->validated();
      // dd($request->validated());
      $data['page_target'] = Str::slug($data['page_target']);

      // Crear popup sin imágenes
      $popup = Popup::create($data);
      Log::info('Popup creado', ['id' => $popup->id]);

      // Guardar Imágenes
      // if ($request->hasFile('image')) {
      //   $data['image'] = $this->imageService->store(
      //     $request->file('image'),
      //     'popups'
      //   );
      // }

      // $popup = Popup::create($data);

      // return response()->json($popup, 201);
      // Guardar imágenes
      Log::info('Popup creado', ['id' => $popup->id]);

      if (!$request->has('images')) {
        Log::warning('No se enviaron imágenes');
      }
      foreach ($request->images ?? [] as $index => $img) {

       Log::info("Procesando imagen {$index}", [
        'keys' => array_keys($img),
        'device' => $img['device'] ?? null,
        'slot' => $img['slot'] ?? null,
        'has_file' => isset($img['file'])
      ]);

      if (!isset($img['file'])) {
        Log::error("Imagen {$index} sin archivo");
        continue;
      }
        $path = $this->imageService->store(
          $img['file'],
          'popups'
        );

        Log::info("Imagen guardada", ['path' => $path]);

        // PopupImage::create([
        //   'popup_id' => $popup->id,
        //   'image'=> $path,
        //   'device' => $img['device'],
        //   'slot' => $img['slot'],
        //   'alt' => $img['alt'] ?? null,
        //   'title' => $img['title'] ?? null
        // ]);
        $popup->images()->create([
          'image' => $path,
          'device' => $img['device'],
          'slot' => $img['slot'],
          'alt' => $img['alt'] ?? null,
          'title' => $img['title'] ?? null,
        ]);
      }
      DB::commit();
      Log::info('STORE POPUP - éxito', ['popup_id' => $popup->id]);

      return response()->json($popup->load('images'), 201);
    } catch (Exception $e) {
      DB::rollBack();

      Log::error('Error al crear popup', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return response()->json([
        'message' => 'Error al crear popup'
      ], 500);
    }
  }

  /**
 * @OA\Patch(
 *     path="/api/admin/popups/{id}",
 *     tags={"Popups"},
 *     summary="Actualizar popup",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Nuevo título"),
 *             @OA\Property(property="button_text", type="string", example="Comprar"),
 *             @OA\Property(property="button_color", type="string", example="#000000"),
 *             @OA\Property(property="page_target", type="string", example="home"),
 *             @OA\Property(property="delay_seconds", type="integer", example=3),
 *             @OA\Property(property="priority", type="integer", example=2),
 *             @OA\Property(property="active", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Popup actualizado"),
 *     @OA\Response(response=404, description="No encontrado"),
 *     @OA\Response(response=422, description="Error de validación")
 * )
 */
  public function update(UpdatePopupRequest $request, $id)
  {

  DB::beginTransaction();
    try {
      $popup = Popup::findOrFail($id);

      $data = $request->validated();
      $data['page_target'] = Str::slug($data['page_target']);


      // if ($request->hasFile('image')) {

      //   $data['image'] = $this->imageService->update(
      //     $request->file('image'),
      //     $popup->image,
      //     'popups'
      //   );
      // }

      // // Solo tocar imágenes si vienen en request
      // if($request->has('images')){
      //   // Eliminar archivos físicos
      //   foreach ($popup->images as $image) {
      //     $this->imageService->remove($image->image);
      //   }
      //   // Eliminar registros
      //   $popup->images()->delete();

      //   // Recrear
      //   foreach ($request->images as $img) {
      //     $path = $this->imageService->store($img['file'], 'popups');

      //     $popup->images()->create([
      //       'image' => $path,
      //       'device' => $img['device'],
      //       'slot' => $img['slot'],
      //       'alt' => $img['alt'] ?? null,
      //       'title' => $img['title'] ?? null
      //     ]);
      //   }
      // }

      // NO sobrescribir product_id si no viene en el request
      if(!$request->has('product_id')){
        unset($data['product_id']);
      }

      $popup->update($data);
      DB::commit();

      return response()->json($popup->load('images'));

    } catch (Exception $e) {
      DB::rollBack();
      Log::error('Error al actualizar popup', [
        'error' => $e->getMessage()
      ]);

      return response()->json([
        'message' => 'Error al actualizar popup'
      ], 500);
    }

  }

  /**
 * @OA\Delete(
 *     path="/api/admin/popups/{id}",
 *     tags={"Popups"},
 *     summary="Eliminar popup",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Popup eliminado"),
 *     @OA\Response(response=404, description="No encontrado")
 * )
 */
  public function destroy($id)
  {
    try {
      // $popup = Popup::findOrFail($id);


      // if ($popup->image) {
      //   $this->imageService->remove($popup->image);
      // }
      // $popup->delete();
      $popup = Popup::with('images')->findOrFail($id);
      foreach ($popup->images as $image) {
        $this->imageService->remove($image->image);
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
  /**
 * @OA\Get(
 *     path="/api/popups",
 *     tags={"Popups"},
 *     summary="Obtener popup activo por página",
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=true,
 *         description="Slug de la página",
 *         @OA\Schema(type="string", example="inicio")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Popup activo"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Parámetro page requerido"
 *     )
 * )
 */
  public function getPopup(Request $request)
  {
    $page = $request->query('page');
    $productId = $request->query('product_id');
    if (! $page) {
      return response()->json(['message' => 'El parámetro "page" es obligatorio'], 400);
    }



    // $popup = Popup::active()
    //   ->forPage($page)
    //   ->inSchedule()
    //   ->orderBy('priority')
    //   ->first();
    // $popup = Popup::active()
    // ->forPage($page)
    // ->inSchedule()
    // ->with('images')
    // ->orderBy('priority')
    // ->first();

    $query = Popup::active()
      ->inSchedule()
      ->with('images')
      ->where(function ($q) use ($page) {
        $q->where('page_target', $page)
          ->orWhere('page_target', 'all');
      });

      // Lógica inteligente
      if($page === 'product-detail'){
        $query->where(function ($q) use ($productId) {
          if($productId){
            $q->where('product_id', $productId);
          }

          // o popup genérico
          $q->orWhereNull('product_id');
        })
        // prioridad: específico primero
        ->orderByRaw('product_id IS NULL'); // false primero
      }

      $popup = $query->orderBy('priority')->first();

    return response()->json($popup);
  }
}
