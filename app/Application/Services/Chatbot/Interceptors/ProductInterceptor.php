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
                        'products' => $products->values()->toArray()
                    ]
                );
            }


            // fallback defensivo (por si algo raro pasa)
            return InterceptionResult::continue($message);

  }
}
