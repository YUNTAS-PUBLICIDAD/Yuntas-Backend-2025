<?php

namespace App\Application\Services\Chatbot\Interceptors;

use App\Application\Services\Chatbot\Context\ChatContext;
use App\Application\Services\Chatbot\Interceptors\InterceptionResult;
use App\Application\Services\Chatbot\Interceptors\Interceptor;
use App\Application\Services\Product\ProductDetector;
use App\Application\Services\Product\ProductService;

class ProductInterceptor implements Interceptor
{
  public function handle($conversation, $message, ChatContext $context): InterceptionResult
  {

    $detector = app(ProductDetector::class);
            $type = $detector->detectType($message);

            // 🚫 No es intención de producto → seguir flujo normal
            if ($type === 'none') {
                return InterceptionResult::continue($message);
            }

            $productService = app(ProductService::class);

            // 🛍️ CASO 1: Exploración (listar productos)
            if ($type === 'list') {
                $products = $productService->getFeaturedForChatbot();

                return InterceptionResult::stopWithMetadata(
                    'Te paso algunas opciones que podrían servirte 👇',
                    [
                        'type' => 'products',
                        'products' => $products->values()
                    ]
                );
            }

            // 🔎 CASO 2: Búsqueda específica
            if ($type === 'search') {
                $products = $productService->searchForChatbot($message);

                // ❌ No encontró → fallback inteligente
                if ($products->isEmpty()) {
                    $fallback = $productService->getFeaturedForChatbot();

                    return InterceptionResult::stopWithMetadata(
                    'No encontré eso exacto, pero mira esto 👇',

                        [
                            'type' => 'products',
                            'products' => $fallback->values()
                        ]
                    );
                }

                // ✅ Encontró coincidencias
                return InterceptionResult::stopWithMetadata(
                    'Esto encaja con lo que buscas 👇',
                    [
                        'type' => 'products',
                        'products' => $products->values()
                    ]
                );
            }

            // fallback defensivo (por si algo raro pasa)
            return InterceptionResult::continue($message);

    // if(!app(ProductDetector::class)->match($message)){
    //   return InterceptionResult::continue($message);
    // }
    // $products = app(ProductService::class)->searchForChatbot($message);

    // if($products->isEmpty()){
    //   return InterceptionResult::stop(
    //   'No encontre productos especificos, pero trabajamos con LED, neón y letreros. ¿Qué necesitas exactamente'
    //   );
    // }
    // $list = $products->map(fn($p) => "- {$p->name}")->implode("\n");
    // $list = $products->map(function ($p) {
    //     return "{$p['name']} - S/ {$p['price']}\n{$p['url']}";
    // })->implode("\n\n");

    // return InterceptionResult::stop(
    // "Tenemos:\n{$list}\n\n¿Quieres cotizar alguno?"
    // );

    // return InterceptionResult::stopWithMetadata(
    //   'Estos son algunos productos 👇',
    //   [
    //     'type' => 'products',
    //     'products' => $products->values()
    //   ]
    // );
  }
}
