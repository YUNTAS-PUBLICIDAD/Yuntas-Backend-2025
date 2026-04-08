<?php

namespace App\Application\Services\Chatbot\Interceptors;

use App\Application\Services\Chatbot\Context\ChatContext;
use App\Application\Services\Chatbot\MessageParser;

class NameInterceptor implements Interceptor
{
  public function handle($conversation, $message, ChatContext $context): InterceptionResult
  {
    $name = app(MessageParser::class)->extractName($message);
    if(!$name){
      return InterceptionResult::continue($message);
    }
    if(!$context->name){
      $context->name = $name;
    }
    $clean = preg_replace('/(me llamo|soy|mi nombre es)\s+' . preg_quote($name, '/') . '/i', '', $message);

    return InterceptionResult::continue(trim($clean));
  }
}
