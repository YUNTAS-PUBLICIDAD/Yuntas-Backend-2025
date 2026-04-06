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
    if(!app(ProductDetector::class)->match($message)){
      return InterceptionResult::continue($message);
    }
    $products = app(ProductService::class)->searchForChatbot($message);

    if($products->isEmpty()){
      return InterceptionResult::stop(
      'No encontre productos especificos, pero trabajamos con LED, neón y letreros. ¿Qué necesitas exactamente'
      );
    }
    $list = $products->map(fn($p) => "- {$p->name}")->implode("\n");

    return InterceptionResult::stop(
    "Tenemos:\n{$list}\n\n¿Quieres cotizar alguno?"
    );
  }
}
