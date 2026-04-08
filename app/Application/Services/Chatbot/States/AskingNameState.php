<?php

namespace App\Application\Services\Chatbot\States;

use App\Application\Services\Chatbot\Context\ChatContext;

class AskingNameState implements State
{
  public function handle($conversation, $message, ChatContext $context): ?string
  {
    if(!$context->name){
      return '¿Cómo te llamas?';
    }
    $context->state = 'ready';

    return null;
  }
}
