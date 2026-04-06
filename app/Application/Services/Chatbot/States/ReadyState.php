<?php

namespace App\Application\Services\Chatbot\States;

use App\Application\Services\Chatbot\Context\ChatContext;

class ReadyState implements State
{
  public function handle($conversation, $message, ChatContext $context): ?string
  {
    return null; // delega al intent
  }
}
