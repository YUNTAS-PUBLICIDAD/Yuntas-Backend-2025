<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Http\Requests\Template\StoreTemplateRequest;
use App\Http\Requests\Template\UpdateTemplateRequest;
use App\Models\Template;
use App\Models\TemplateContent;
use App\Service\Image\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TemplateController extends Controller
{
  private ImageService $imageService;

public function __construct(ImageService $imageService)
{
  $this->imageService = $imageService;
}


/**
 * @OA\Get(
 *     path="/api/admin/templates",
 *     tags={"Templates"},
 *     summary="Listar templates",
 *     @OA\Response(
 *         response=200,
 *         description="Lista de templates",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="active", type="boolean"),
 *                 @OA\Property(
 *                     property="contents",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/TemplateContent")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
    // Lista templates
    public function index()
    {
      return Template::with('contents.buttons')->get();
    }

    /**
 * @OA\Get(
 *     path="/api/admin/templates/{id}",
 *     tags={"Templates"},
 *     summary="Obtener template",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Template encontrado"),
 *     @OA\Response(response=404, description="No encontrado")
 * )
 */
    // Mostrar un template específico
    public function show($id)
    {
      return Template::with('contents.buttons')->findOrFail($id);
    }

    /**
 * @OA\Post(
 *     path="/api/admin/templates",
 *     tags={"Templates"},
 *     summary="Crear template",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"lead_source_id","name","active"},
 *
 *                 @OA\Property(property="lead_source_id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Template bienvenida"),
 *                 @OA\Property(property="active", type="boolean", example=true),
 *
 *                 @OA\Property(
 *                     property="contents",
 *                     type="array",
 *                     description="Enviar como contents[0][field]. Ej: contents[0][channel]",
 *                     @OA\Items(ref="#/components/schemas/TemplateContent")
 *                 ),
 *
 *                 @OA\Property(property="contents[0][channel]", type="string", example="whatsapp"),
 *                 @OA\Property(property="contents[0][subject]", type="string", example="Promo"),
 *                 @OA\Property(property="contents[0][content]", type="string", example="Hola {{name}}"),
 *                 @OA\Property(property="contents[0][active]", type="boolean", example=true),
 *                 @OA\Property(property="contents[0][image]", type="string", format="binary")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Template creado"),
 *     @OA\Response(response=422, description="Error de validación")
 * )
 */
    // Crer template + contenidos
    public function store(StoreTemplateRequest $request)
    {

    try {
      DB::beginTransaction();
       $data = $request->validated();

        $template = Template::create([
            'lead_source_id' => $data['lead_source_id'],
            'name' => $data['name'],
            'active' => $data['active']
        ]);

            foreach ($data['contents'] ?? [] as $i => $contentData) {

              $buttons = $contentData['buttons'] ?? [];
              unset($contentData['buttons']);

                // Imagen (consistente)
                $file = $request->file("contents.$i.image");
                if ($file) {
                    $contentData['image_url'] = $this->imageService->store($file, 'plantillas');
                }
                unset($contentData['image']);

                // ⚠️ Evitar inserts vacíos
                if (
                    empty($contentData['content']) &&
                    empty($contentData['image_url'])
                ) {
                    continue;
                }
                // Guardar referencia
                $content = $template->contents()->create($contentData);

                $this->syncButtons($content, $buttons);
            }

        DB::commit();

        return $template->load('contents.buttons');

    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('ERROR STORE TEMPLATE', [
        'message' => $e->getMessage(),
      ]);

      return response()->json([
        'message' => 'Error al crear template'
      ], 500);
    }
    }

    /**
 * @OA\Patch(
 *     path="/api/admin/templates/{id}",
 *     tags={"Templates"},
 *     summary="Actualizar template",
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="active", type="boolean"),
 *
 *                 @OA\Property(
 *                     property="contents",
 *                     type="array",
 *                     description="Actualizar o crear contenidos usando contents[index][field]",
 *                     @OA\Items(ref="#/components/schemas/TemplateContent")
 *                 ),
 *
 *                 @OA\Property(property="contents[0][id]", type="integer", example=1),
 *                 @OA\Property(property="contents[0][channel]", type="string"),
 *                 @OA\Property(property="contents[0][content]", type="string"),
 *                 @OA\Property(property="contents[0][active]", type="boolean"),
 *                 @OA\Property(property="contents[0][image]", type="string", format="binary")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Template actualizado"),
 *     @OA\Response(response=404, description="No encontrado")
 * )
 */
    // Actualizar template + contenidos
    public function update(UpdateTemplateRequest $request, $id)
    {
      try {
        DB::beginTransaction();

        $template = Template::with('contents.buttons')->findOrFail($id);

        $data = $request->validated();

        // 🔹 Solo campos del template
        $template->update(
            collect($data)->except('contents')->toArray()
        );


        $existingContents = $template->contents()->get()->keyBy('id');

        $idsToKeep = [];

            foreach ($data['contents'] ?? [] as $i => $contentData) {

              $buttons = $contentData['buttons'] ?? null;
              unset($contentData['buttons']);

                // 🔹 Buscar SIEMPRE dentro del template
                $content = isset($contentData['id'])
                    ? $existingContents->get($contentData['id']) : null;
                    // ? $template->contents()->where('id', $contentData['id'])->first()
                    // : null;

                // 🔹 Imagen mismo formato que store
                $file = $request->file("contents.$i.image");

                if ($file) {
                    $contentData['image_url'] = $content
                        ? $this->imageService->update($file, $content->image_url, 'plantillas')
                        : $this->imageService->store($file, 'plantillas');
                }
                unset($contentData['image']);

                // Filtro
                if(
                empty($contentData['content']) && empty($contentData['image_url'])
                ){
                  continue;
                }

                if ($content) {
                    $content->update($contentData);
                } else {
                    // Fix: reasignar
                    $content = $template->contents()->create($contentData);
                }
                $idsToKeep[] = $content->id;
                // Sync botones SOLO si vienen
                if(!is_null($buttons)){
                  $this->syncButtons($content, $buttons);
                }
        }

        // $template->contents()
        //   ->whereNotIn('id', $idsToKeep)
        //   ->delete();

        if (array_key_exists('contents', $data)){
          $template->contents()
          ->whereNotIn('id', $idsToKeep)
          ->delete();
        }

        DB::commit();

        return $template->load('contents.buttons');

    } catch (\Throwable $e) {

        DB::rollBack();

        Log::error('ERROR UPDATE TEMPLATE', [
            'message' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => 'Error al actualizar template'
        ], 500);
    }
    }

    /**
 * @OA\Delete(
 *     path="/api/admin/templates/{id}",
 *     tags={"Templates"},
 *     summary="Eliminar template",
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(response=200, description="Eliminado"),
 *     @OA\Response(response=404, description="No encontrado")
 * )
 */
    // Eliminar template
    public function destroy($id)
    {
      $template = Template::findOrFail($id);
      // $template->contents()->delete();
      $template->delete();
      return response()->json(['message' => 'Template eliminado']);
    }

    // CORE: Sync botones
    private function syncButtons($content, array $buttonsData)
    {
      $existing = $content->buttons()->get()->keyBy('id');

      $idsToKeep = [];

      foreach ($buttonsData as $buttonData){
        if(isset($buttonData['id']) && $existing->has($buttonData['id'])){
          $button = $existing[$buttonData['id']];
          $button->update($buttonData);
          $idsToKeep[] = $button->id;
        }else {
          $new = $content->buttons()->create($buttonData);
          $idsToKeep[] = $new->id;
        }
      }
      $content->buttons()
        ->whereNotIn('id', $idsToKeep)
        ->delete();
    }

}
