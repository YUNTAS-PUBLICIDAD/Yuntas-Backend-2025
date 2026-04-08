<?php
namespace App\Application\Services\Chatbot\States;

use App\Application\Services\Chatbot\Context\ChatContext;

class StateResolver
{
  public function resolve(ChatContext $context): State
  {
    return match($context->state){
      'asking_name' => app(AskingNameState::class),
      'asking_project_type' => app(AskingProjectTypeState::class),
      'asking_contact' => app(AskingContactState::class),
      default => app(ReadyState::class)
    };
  }
}
