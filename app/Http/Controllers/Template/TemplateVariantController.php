<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplateVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateVariantController extends Controller
{

  public function index($templateId)
  {
      return TemplateVariant::with('assets')
          ->where('template_id', $templateId)
          ->get();
  }

    public function store(Request $request, $templateId)
    {
        $template = Template::findOrFail($templateId);

        $data = $request->validate([
            'channel' => 'required|string|in:whatsapp,email',
            'context' => 'required|string',
            'subject' => 'nullable|string',
            'content' => 'required|string',
            'variables' => 'array',
            'active' => 'boolean',
            'assets' => 'array',
        ]);

        return DB::transaction(function () use ($template, $data) {

            $variant = $template->variants()->create([
                'channel' => $data['channel'],
                'context' => $data['context'],
                'subject' => $data['subject'] ?? null,
                'content' => $data['content'],
                'variables' => $data['variables'] ?? [],
                'active' => $data['active'] ?? true,
            ]);

            if (!empty($data['assets'])) {
                $variant->assets()->createMany(
                    array_map(fn ($asset) => [
                        'key' => $asset['key'],
                        'path' => $asset['path'],
                        'meta' => $asset['meta'] ?? null,
                    ], $data['assets'])
                );
            }

            return response()->json(
                $variant->load('assets'),
                201
            );
        });
    }

    public function update(Request $request, $id)
    {
        $variant = TemplateVariant::findOrFail($id);

        $data = $request->validate([
            'channel' => 'sometimes|string|in:whatsapp,email',
            'context' => 'sometimes|string',
            'subject' => 'nullable|string',
            'content' => 'sometimes|string',
            'variables' => 'array',
            'active' => 'boolean',
            'assets' => 'array',
        ]);

        return DB::transaction(function () use ($variant, $data) {

            $variant->update($data);

            if (array_key_exists('assets', $data)) {
                $variant->assets()->delete();

                if (!empty($data['assets'])) {
                    $variant->assets()->createMany($data['assets']);
                }
            }

            return response()->json($variant->load('assets'));
        });
    }

    public function destroy($id)
    {
        TemplateVariant::findOrFail($id)->delete();

        return response()->json(['message' => 'deleted']);
    }
}
