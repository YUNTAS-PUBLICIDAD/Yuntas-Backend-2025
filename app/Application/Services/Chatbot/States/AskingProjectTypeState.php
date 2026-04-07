<?php
namespace App\Application\Services\Chatbot\States;

use App\Application\Services\Chatbot\Context\ChatContext;

class AskingProjectTypeState implements State
{
  public function handle($conversation, $message, ChatContext $context):?string
  {
    if(strlen(trim($message)) < 3){
      return 'Dame mas detalle del proyecto';
    }
    $context->data['project_type'] = $message;
    $context->state = 'asking_contact';

    return 'Para cotizarte, ¿me dejas tú número?';
  }
}
