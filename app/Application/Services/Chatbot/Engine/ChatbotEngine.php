<?php

namespace App\Application\Services\Chatbot\Engine;

use App\Application\Services\Chatbot\Actions\ActionExecutor;
use App\Application\Services\Chatbot\Context\ChatContext;
use App\Application\Services\Chatbot\Engine\Pipeline;
use App\Application\Services\Chatbot\Formatters\ResponseFormatter;
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
  public function handleMessage($conversation, string $message, string $channel = 'web')
  {

    $this->cleanupIfNeeded();

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

      // return $this->send($conversation, $response);
      // return $this->send(
      // $conversation,
      // $response['text'],
      // $response['metadata'] ?? null
      // );
      $formatted = app(ResponseFormatter::class)
        ->format($response['text'], $response['metadata'] ?? null, $channel);

    // return $this->send($conversation, $formatted);
    // $this->send($conversation, $formatted['text']);
    $this->send(
    $conversation,
    $formatted['text'],
    $formatted['metadata'] ?? null
    );
  }

  protected function handleIntent($conversation, $message, $context)
  {
    $intent = app(IntentMatcher::class)->match($message);

    if(!$intent){
      // return 'No entendí, ¿puedes reformular?';
      return [
        'text' => 'No entendí, ¿puedes reformular?',
        'metadata' => null
      ];
    }


    $question = $intent->questions()->inRandomOrder()->first();
    $answer = $question?->answers()->inRandomOrder()->first();

    if(!$answer){
      // return 'No tengo respuesta para eso aún.';
      return [
        'text' => 'No tengo respuesta para eso aún',
        'metadata' => null
      ];
    }

    $metadata = app(ActionExecutor::class)->execute(
    $intent,
    $answer,
    $conversation,
    $message,
    $context
    );

    // return $this->parse($answer->answer_text, $context);
    return [
    'text' => $this->parse($answer->answer_text, $context),
    'metadata' => $metadata
    ];
  }

  protected function storeMessage($conversation, $message)
  {
    $conversation->messages()->create([
     'message_text' => $message,
     'sender' => 'user'
    ]);
    $conversation->touch();
  }

  protected function send(
  $conversation,
  $text,
  $metadata = null
  )
  {
    $conversation->messages()->create([
    'message_text' => $text,
    'sender' => 'bot',
    'metadata' => $metadata
    ]);
    $conversation->touch();

    $this->pruneMessages($conversation);

    // return $text;
    return [
      'text' => is_array($text) ? $text['text'] : $text,
      'metadata' => $metadata
    ];
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

  protected function pruneMessages($conversation)
  {
    $limit = 20; // Ajusta según UX
    $idsToKeep = $conversation->messages()
      ->latest()
      ->take($limit)
      ->pluck('id');
      $conversation->messages()
      ->whereNotIn('id', $idsToKeep)
      ->delete();
  }

  protected function cleanupIfNeeded()
  {
    cache()->remember('chatbot_cleanup', 3600, function () {
      ChatbotConversation::where('updated_at', '<', now()->subDay())->delete();
    });
  }

}
