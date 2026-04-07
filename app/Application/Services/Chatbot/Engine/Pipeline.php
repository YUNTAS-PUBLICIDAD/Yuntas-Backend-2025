<?php

namespace App\Application\Services\Chatbot\Engine;

use App\Application\Services\Chatbot\Interceptors\CtaInterceptor;
use App\Application\Services\Chatbot\Interceptors\InterceptionResult;
use App\Application\Services\Chatbot\Interceptors\NameInterceptor;
use App\Application\Services\Chatbot\Interceptors\PhoneInterceptor;
use App\Application\Services\Chatbot\Interceptors\ProductInterceptor;

class Pipeline
{
  protected array $interceptors = [
  CtaInterceptor::class,
  NameInterceptor::class,
  PhoneInterceptor::class,
  ProductInterceptor::class
  ];

  public function processInterceptors($conversation, $message, $context)
  {
    foreach ($this->interceptors as $class){
      $result = app($class)->handle($conversation, $message, $context);

      if($result->stop){
        return $result;
      }

      $message = $result->message;
    }

    return InterceptionResult::continue($message);
  }
}
