<?php
namespace App\Application\Services\Chatbot\States;

use App\Application\Services\Chatbot\Context\ChatContext;

class AskingContactState implements State
{
  public function handle($conversation, $message, ChatContext $context): ?string
  {
    if(!$context->phone){
      return 'Necesito tu número para continuar';
    }
    $context->state = 'ready';

    return 'Pefecto, te contactamos 👍';
  }
}
