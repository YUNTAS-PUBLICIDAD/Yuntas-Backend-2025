<?php
namespace App\Application\Services\Template;

use App\Application\Support\TemplateVariableBuilder;
use App\Models\Lead;
use App\Models\ProductTemplateAsset;
use App\Models\Template;
use App\Service\Image\ImageService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TemplateService
{

  public function __construct(private ImageService $imageService){}

  // public function getBySource(int $sourceId, string $channel): ?Template
  // {
  //   // return Template::where('lead_source_id', $sourceId)
  //   // ->where('active', true)
  //   // ->first();
  //   return Template::with(['contents' => function ($q) use ($channel){
  //    $q->where('channel', $channel)
  //    ->where('active', true)
  //    ->with('buttons');
  //   }])
  //   ->where('lead_source_id', $sourceId)
  //   ->where('active', true)
  //   ->first();
  // }

  // public function render(int $sourceId, string $channel, array $data)
  // {
  //   $template = $this->getBySource($sourceId, $channel);
  //   if(!$template) {
  //     throw new Exception("Template no encontrado");
  //   };
  //   // $content = $template->contents->first();
  //   $content = $template->contents->where('channel', $channel)->where('active', true)->sortByDesc('id')->first();

  //   if(!$content){
  //     throw new Exception("Contenido no encontrado para canal {$channel}");
  //   };

  //   $this->validateVariables($content, $data);

  //   return [
  //     'message' => $content->render($data),
  //     'subject' => $content->subject ?? null,
  //     'image_url' => $content->image_url ? asset($content->image_url) : null,
  //     'buttons' => $content->buttons ?? []
  //   ];
  // }

  public function getAvailableVariables(): array
  {

    // $preview = $lead ? TemplateVariableBuilder::forLead($lead) : TemplateVariableBuilder::forLead(new Lead());

    return TemplateVariableBuilder::schema();

    // return [
    //   'variables' => array_keys($preview),
    //   'preview' => $preview
    // ];
  }

  // private function validateVariables($content, array $data):array
  // {
  //   // foreach ($content->variables ?? [] as $var) {
  //   //   if (!array_key_exists($var, $data)) {
  //   //     throw new \InvalidArgumentException("Falta variable: {$var} | DATA: " . json_encode($data));
  //   //   }
  //   // }
  //   // Log::info('VALIDANDO VARIABLES', [
  //   //   'esperadas' => $content->variables,
  //   //   'recibidas' => array_keys($data),
  //   // ]);

  //   $missing = [];

  //   foreach ($content->variables ?? [] as $var){
  //     if(!array_key_exists($var, $data)){
  //       $missing[] = $var;
  //       $data[$var] = ''; // fallback automático
  //     }
  //   }

  //   if(!empty($missing)){
  //     Log::warning('Variables faltantes en template', [
  //       'faltantes' => $missing,
  //       'template_id' => $content->template_id ?? null,
  //       'channel' => $content->channel ?? null,
  //     ]);
  //   }
  //   return $data;
  // }

  // public function getByContext(string $channel, string $context): ?Template
  // {
  //   return Template::where('active', true)
  //       ->whereHas('variants', function ($q) use ($channel, $context) {
  //           $q->where('channel', $channel)
  //             ->where('context', $context)
  //             ->where('active', true);
  //       })
  //       ->with(['variants' => function ($q) use ($channel, $context) {
  //           $q->where('channel', $channel)
  //             ->where('context', $context)
  //             ->where('active', true)
  //             ->with(['assets', 'productAssets']);
  //       }])
  //       ->latest()
  //       ->first();
  // }

  // public function renderByContext(
  //   string $channel,
  //   string $context,
  //   array $data,
  //   ?int $productId = null
  // ){
  //   $template = $this->getByContext($channel, $context);

  //      if (!$template) {
  //          throw new Exception("Template no encontrado para {$context}");
  //      }

  //      // $variant = $template->variants->first();
  //      $variant = $template->variants
  //        ->where('channel', $channel)
  //        ->where('context', $context)
  //        ->first();

  //      if (!$variant) {
  //          throw new Exception("Variant no encontrada");
  //      }

  //      // 🔥 imagen dinámica
  //      $imageUrl = null;

  //      // PRIORIDAD 1 → product asset
  //      if ($productId) {
  //          $productAsset = $variant->productAssets
  //              ->where('product_id', $productId)
  //              ->first();

  //          if ($productAsset) {
  //              $imageUrl = asset($productAsset->path);
  //          }
  //      }

  //      // PRIORIDAD 2 → global asset
  //      if (!$imageUrl && $context !== "PRODUCTO") {
  //          $asset = $variant->assets->where('key', 'image')->first();
  //          if ($asset) {
  //              $imageUrl = asset($asset->path);
  //          }
  //      }

  //      $data = $this->validateVariables($variant, $data);

  //      return [
  //          // 'message' => $variant->render($data),
  //          'content' => $variant->render($data),
  //          'subject' => $variant->subject,
  //          'image_url' => $imageUrl,
  //          'cta_text' => $variant->cta_text,
  //          'cta_url' => $variant->cta_url,
  //          'product_assets' => $variant->productAssets->map(fn ($a) => [
  //           'product_id' => $a->product_id,
  //           'path' => $a->path,
  //           'key' => $a->key
  //          ])->values(),
  //      ];
  // }

  public function save(array $data, ?int $id = null)
    {
      return DB::transaction(function () use ($data, $id) {

              // =========================
              // TEMPLATE
              // =========================

              $template = $id
                  ? Template::findOrFail($id)
                  : new Template();

              $template->fill([
                  'name' => $data['name'],
                  'context' => $data['context'],
                  'active' => $data['active'] ?? true,
              ]);

              $template->save();

              // =========================
              // CLEAN OLD
              // =========================

              $template->steps()->delete();

              // =========================
              // STEPS
              // =========================

              foreach (($data['steps'] ?? []) as $stepData) {

                  $step = $template->steps()->create([
                      'step' => $stepData['step'],
                      'delay_value' => $stepData['delay_value'],
                      'delay_unit' => $stepData['delay_unit'],
                      'active' => $stepData['active'] ?? true,
                  ]);

                  // =========================
                  // VARIANTS
                  // =========================

                  foreach (($stepData['variants'] ?? []) as $variantData) {

                      $variant = $step->variants()->create([
                          'channel' => $variantData['channel'],
                          'subject' => $variantData['subject'] ?? null,
                          'content' => $this->decodeContent($variantData['content'] ?? null),
                          'variables' => $variantData['variables'] ?? [],
                          'cta_text' => $variantData['cta_text'] ?? null,
                          'cta_url' => $variantData['cta_url'] ?? null,
                          'active' => $variantData['active'] ?? true,
                      ]);

                      // =========================
                      // ASSETS
                      // =========================

                      foreach (($variantData['assets'] ?? []) as $asset) {

                          $variant->assets()->create([
                              'key' => $asset['key'],
                              'path' => $asset['path'],
                              'meta' => $asset['meta'] ?? null,
                          ]);
                      }

                      // =========================
                      // PRODUCT OVERRIDES
                      // =========================

                      foreach (($variantData['product_overrides'] ?? []) as $override) {

                          $variant->productOverrides()->create([
                              'product_id' => $override['product_id'],
                              'subject' => $override['subject'] ?? null,
                              'content' => $this->decodeContent($override['content'] ?? null),
                              'cta_text' => $override['cta_text'] ?? null,
                              'cta_url' => $override['cta_url'] ?? null,
                              'variables' => $override['variables'] ?? [],
                              'assets' => $override['assets'] ?? [],
                              'active' => $override['active'] ?? true,
                          ]);
                      }
                  }
              }

              return $this->get($template->id);
          });
    }

    public function get(int $id)
    {
        // return Template::with(['variants.assets', 'variants.productAssets'])->findOrFail($id);

        return Template::with([
                'steps.variants.assets',
                'steps.variants.productOverrides'
            ])->findOrFail($id);
    }

    public function list()
    {
        // return Template::with(['variants.assets', 'variants.productAssets'])
        //     ->latest()
        //     ->paginate(20);
        return Template::with([
                'steps.variants.assets',
                'steps.variants.productOverrides'
            ])
            ->latest()
            ->paginate(20);
    }

    public function delete(int $id)
    {
        $template = Template::findOrFail($id);
        $template->delete();

        return ['message' => 'deleted'];
    }

    private function decodeContent(?string $content): ?string
    {
        if (is_null($content)) {
            return null;
        }

        if (str_starts_with($content, 'base64:')) {
            return base64_decode(substr($content, 7));
        }

        return $content;
    }

}
