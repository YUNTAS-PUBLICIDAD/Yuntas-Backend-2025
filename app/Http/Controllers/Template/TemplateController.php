<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Http\Requests\Template\StoreTemplateRequest;
use App\Http\Requests\Template\UpdateTemplateRequest;
use App\Models\Template;
use App\Models\TemplateContent;
use App\Service\Image\ImageService;
use DB;
use Log;

class TemplateController extends Controller
{
  private ImageService $imageService;

public function __construct(ImageService $imageService)
{
  $this->imageService = $imageService;
}
    // Lista templates
    public function index()
    {
      return Template::with('contents')->get();
    }

    // Mostrar un template específico
    public function show($id)
    {
      return Template::with('contents')->findOrFail($id);
    }

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

        if (!empty($data['contents'])) {
            foreach ($data['contents'] as $i => $contentData) {

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

                $template->contents()->create($contentData);
            }
        }

        DB::commit();

        return $template->load('contents');

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

    // Actualizar template + contenidos
    public function update(UpdateTemplateRequest $request, $id)
    {
      try {
        DB::beginTransaction();

        $template = Template::with('contents')->findOrFail($id);

        $data = $request->validated();

        // 🔹 Solo campos del template
        $template->update(
            collect($data)->except('contents')->toArray()
        );

        if (!empty($data['contents'])) {

            foreach ($data['contents'] as $i => $contentData) {

                // 🔹 Buscar SIEMPRE dentro del template
                $content = isset($contentData['id'])
                    ? $template->contents()->where('id', $contentData['id'])->first()
                    : null;

                // 🔹 Archivo
                $file = $request->file("contents_{$i}_image");

                if ($file) {
                    $contentData['image_url'] = $content
                        ? $this->imageService->update($file, $content->image_url, 'plantillas')
                        : $this->imageService->store($file, 'plantillas');
                }

                if ($content) {
                    $content->update($contentData);
                } else {
                    $template->contents()->create($contentData);
                }
            }
        }

        DB::commit();

        return $template->load('contents');

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

    // Eliminar template
    public function destroy($id)
    {
      $template = Template::findOrFail($id);
      $template->contents()->delete();
      $template->delete();
      return response()->json(['message' => 'Template eliminado']);
    }

}
