<?php

namespace App\Application\Services\Chatbot\Engine;

use App\Application\Services\Chatbot\Actions\ActionExecutor;
use App\Application\Services\Chatbot\Context\ChatContext;
use App\Application\Services\Chatbot\Engine\Pipeline;
use App\Application\Services\Chatbot\Intent\IntentMatcher;
use App\Application\Services\Chatbot\States\StateResolver;
use App\Models\ChatbotConversation;

// INPUT
//  ↓
// [1] Global Interceptors (nombre, phone, etc)
//  ↓
// [2] STATE MACHINE (manda SIEMPRE)
//    ├─ si el estado responde → FIN
//    └─ si el estado delega → continuar
//  ↓
// [3] INTENT ENGINE
//  ↓
// [4] ACTION PIPELINE
//  ↓
// OUTPUT

// Encargado de orquestar todo
class ChatbotEngine
{
  public function handleMessage($conversation, string $message)
  {

    $this->storeMessage($conversation, $message);

    $context = ChatContext::fromArray($conversation->context);

    // Interceptores
    $interception = app(Pipeline::class)
      ->processInterceptors($conversation, $message, $context);

      if($interception->stop){
        return $this->send($conversation, $interception->response, $interception->metadata ?? null);
      }

      $message = $interception->message;

      // FSM
      $state = app(StateResolver::class)->resolve($context);

      $response = $state->handle($conversation, $message, $context);

      if($response){
        $this->persistContext($conversation, $context);
        return $this->send($conversation, $response);
      }

      // Intent
      $response = $this->handleIntent($conversation, $message ,$context);

      $this->persistContext($conversation, $context);

      return $this->send($conversation, $response);
  }

  protected function handleIntent($conversation, $message, $context)
  {
    $intent = app(IntentMatcher::class)->match($message);

    if(!$intent){
      return 'No entendí, ¿puedes reformular?';
    }

    $question = $intent->questions()->inRandomOrder()->first();
    $answer = $question?->answers()->inRandomOrder()->first();

    if(!$answer){
      return 'No tengo respuesta para eso aún.';
    }

    app(ActionExecutor::class)->execute(
    $intent,
    $answer,
    $conversation,
    $message,
    $context
    );

    return $this->parse($answer->answer_text, $context);
  }

  protected function storeMessage($conversation, $message)
  {
    $conversation->messages()->create([
     'message_text' => $message,
     'sender' => 'user'
    ]);
  }

  protected function send($conversation, $text, $metadata = null)
  {
    $conversation->messages()->create([
    'message_text' => $text,
    'sender' => 'bot',
    'metadata' => $metadata
    ]);
  }

  protected function persistContext($conversation, $context)
  {
    $conversation->update([
    'context' => $context->toArray()
    ]);
  }

  protected function  parse($text, $context)
  {
    return preg_replace_callback('/{{(.*?)}}/', function ($m) use ($context) {
               return data_get($context->toArray(), trim($m[1]), '');
           }, $text);
  }

}
