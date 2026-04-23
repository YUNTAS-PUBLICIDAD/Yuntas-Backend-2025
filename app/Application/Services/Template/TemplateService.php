<?php
namespace App\Application\Services\Template;
use App\Models\Template;
use Exception;
use Illuminate\Support\Facades\Log;

class TemplateService
{

  public function getBySource(int $sourceId, string $channel): ?Template
  {
    // return Template::where('lead_source_id', $sourceId)
    // ->where('active', true)
    // ->first();
    return Template::with(['contents' => function ($q) use ($channel){
     $q->where('channel', $channel)
     ->where('active', true)
     ->with('buttons');
    }])
    ->where('lead_source_id', $sourceId)
    ->where('active', true)
    ->first();
  }

  public function render(int $sourceId, string $channel, array $data)
  {
    $template = $this->getBySource($sourceId, $channel);
    if(!$template) {
      throw new Exception("Template no encontrado");
    };
    // $content = $template->contents->first();
    $content = $template->contents->where('channel', $channel)->where('active', true)->sortByDesc('id')->first();

    if(!$content){
      throw new Exception("Contenido no encontrado para canal {$channel}");
    };

    $this->validateVariables($content, $data);

    return [
      'message' => $content->render($data),
      'subject' => $content->subject ?? null,
      'image_url' => $content->image_url ? asset($content->image_url) : null,
      'buttons' => $content->buttons ?? []
    ];
  }

  // public function getByProduct(int $productId, int $step, string $channel): ?Template
  // {
  //   return Template::with(['contents' => function ($q) use ($channel, $step){
  //     $q->where('channel', $channel)
  //     ->where('step', $step)
  //     ->where('active', true);
  //   }])->where('product_id', $productId)
  //   ->where('active', true)
  //   ->first();
  // }
  // public function getByProduct(
  //   int $productId,
  //   string $channel,
  //   // int $sourceId
  //   ): ?Template
  // {
  //   return Template::with(['contents' => function ($q) use ($channel){
  //     $q->where('channel', $channel)
  //     ->where('active', true);
  //   }])
  //   ->where('product_id', $productId)
  //   // ->where('lead_source_id', $sourceId)
  //   ->whereNull('lead_source_id')
  //   ->where('active', true)
  //   ->first();
  // }

  // public function renderByProduct(int $productId, int $step, string $channel, array $data)
  // {
  //   $template = $this->getByProduct($productId, $step, $channel);

  //   if (!$template) {
  //     throw new Exception("Template no encontrado para producto {$productId} paso {$step}", 1);
  //   }
  //   $content = $template->contents->first();
  //   if (!$content) {
  //     throw new Exception("Contenido no encontrado para canal {$channel} paso {$step}");
  //   }
  //   $this->validateVariables($content, $data);

  //   return [
  //     'message' => $content->render($data),
  //     'subject' => $content->subject ?? null,
  //     'image_url' => $content->image_url ?? null
  //   ];
  // }

  // public function renderByProduct(
  //   int $productId,
  //   // int $sourceId,
  //   string $channel, array $data)
  // {
  //   $template = $this->getByProduct($productId, $channel);

  //   if (!$template) {
  //     throw new Exception("Template no encontrado para producto {$productId}");
  //   }
  //   $content = $template->contents->where('channel', $channel)->where('active', true)->sortByDesc('id')->first();

  //   if (!$content) {
  //     throw new Exception("Contenido no encontrado para canal {$channel}");
  //   }
  //   $this->validateVariables($content, $data);

  //   return [
  //     'message' => $content->render($data),
  //     'subject' => $content->subject ?? null,
  //     'image_url' => $content->image_url ?? null
  //   ];
  // }



  private function validateVariables($content, array $data):void
  {
    foreach ($content->variables ?? [] as $var) {
      if (!array_key_exists($var, $data)) {
        throw new \InvalidArgumentException("Falta variable: {$var} | DATA: " . json_encode($data));
      }
    }
    Log::info('VALIDANDO VARIABLES', [
      'esperadas' => $content->variables,
      'recibidas' => array_keys($data),
    ]);
  }
}
