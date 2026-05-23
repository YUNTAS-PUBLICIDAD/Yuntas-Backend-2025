<?php

namespace App\Application\Services\Chatbot\Engine;

use App\Application\Services\Chatbot\Actions\ActionExecutor;
use App\Application\Services\Chatbot\Context\ChatContext;
use App\Application\Services\Chatbot\Engine\Pipeline;
use App\Application\Services\Chatbot\Flows\ProductDiscoveryFlow;
use App\Application\Services\Chatbot\Formatters\ResponseFormatter;
use App\Application\Services\Chatbot\Intent\IntentMatcher;
use App\Application\Services\Chatbot\States\StateResolver;
use App\Models\ChatbotConversation;
use App\Application\Services\Product\ProductService;
use Illuminate\Support\Facades\Log;

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

    $sentMessages = [];

    $this->cleanupIfNeeded();

    $this->storeMessage($conversation, $message);

    $context = ChatContext::fromArray($conversation->context);

    Log::info('CHATBOT: context loaded', [
    'context' => $context->toArray()
    ]);

    // Interceptores
    $interception = app(Pipeline::class)
      ->processInterceptors($conversation, $message, $context);

      if($interception->stop){
        // return $this->send($conversation, $interception->response, $interception->metadata ?? null);
        $formatted = app(ResponseFormatter::class)
        ->format($interception->response, !empty($interception->metadata) ? $interception->metadata : null, $channel);

        Log::info('CHATBOT interceptor formatted', [
          'formatted' => $formatted
        ]);

        $sentMessages[] = $this->send(
          $conversation,
          $formatted['text'],
          $formatted['metadata'] ?? null
        );

        return $sentMessages;
      }

      $message = $interception->message;

      // FSM
      $state = app(StateResolver::class)->resolve($context);

      $response = $state->handle($conversation, $message, $context);

      if($response){
        $this->persistContext($conversation, $context);
        // return $this->send($conversation, $response);
        $sentMessages[] = $this->send(
          $conversation,
          $response
        );

        return $sentMessages;
      }

      // Intentar iniciar flow
      app(FlowTriggerResolver::class)->tryStart($message, $context);

      Log::info('CHATBOT: after trigger resolver', [
        'flow' => data_get($context->data, 'flow')
      ]);

      // Si existe flow activo
      if(data_get($context->data, 'flow.active')){
        Log::info('CHATBOT: entering flow engine');
        // $flowResponses = app(FlowEngine::class)
        // ->handle($conversation, $message, $context);
        // Log::info('CHATBOT: flow responses', [
        //   'responses' => $flowResponses
        // ]);

        // if(!empty($flowResponses)){
        //   $this->persistContext($conversation, $context);

        //   foreach($flowResponses as $res){
        //     $sentMessages[] = $this->send(
        //     $conversation,
        //     $res['text'],
        //     $res['metadata'] ?? null
        //     );
        //   }
        //   return $sentMessages;
        // }

        $flowType = data_get(
               $context->data,
               'flow.type'
           );

           // CONVERSATIONAL FLOW
           if($flowType === 'conversational'){

               $flowName = data_get(
                   $context->data,
                   'flow.name'
               );

               if($flowName === 'product_discovery'){

                   $response = app(ProductDiscoveryFlow::class)
                       ->handle($message, $context);

                   $this->persistContext(
                       $conversation,
                       $context
                   );

                   $sentMessages[] = $this->send(
                       $conversation,
                       $response['text'],
                       $response['metadata'] ?? null
                   );

                   return $sentMessages;
               }
           }

           // VISUAL FLOW
           if($flowType === 'visual'){

               $flowResponses = app(FlowEngine::class)
                   ->handle(
                       $conversation,
                       $message,
                       $context
                   );

               if(!empty($flowResponses)){

                   $this->persistContext(
                       $conversation,
                       $context
                   );

                   foreach($flowResponses as $res){

                       $sentMessages[] = $this->send(
                           $conversation,
                           $res['text'],
                           $res['metadata'] ?? null
                       );
                   }

                   return $sentMessages;
               }
           }
      }

      // $started = app(FlowTriggerResolver::class)->tryStart($message, $context);

      // if($started){
      // $flowResponses = app(FlowEngine::class)
      // ->handle($conversation, $message, $context);

      // if(!empty($flowResponses)){
      //   $this->persistContext($conversation, $context);

      //   foreach($flowResponses as $res){
      //     $sentMessages[] = $this->send(
      //     $conversation,
      //     $res['text'],
      //     $res['metadata'] ?? null
      //     );
      //   }
      //   return $sentMessages;
      // }
      // }

      // FLOW ENGINE
      // $flowResponse = app(FlowEngine::class)
      // ->handle($conversation, $message, $context);

      // if($flowResponse){
      //   $this->persistContext($conversation, $context);

      //   return $this->send(
      //   $conversation,
      //   $flowResponse['text'],
      //   $flowResponse['metadata'] ?? null
      //   );
      // }


      Log::info('CHATBOT: entering intent engine', [
        'message' => $message
      ]);
      // Intent
      $response = $this->handleIntent($conversation, $message ,$context);
      Log::info('CHATBOT: raw intent response', [
        'response' => $response
      ]);

      $this->persistContext($conversation, $context);

      // return $this->send($conversation, $response);
      // return $this->send(
      // $conversation,
      // $response['text'],
      // $response['metadata'] ?? null
      // );
      $formatted = app(ResponseFormatter::class)
        ->format($response['text'], $response['metadata'] ?? null, $channel);

        Log::info('CHATBOT: formatted response', [
          'formatted' => $formatted
        ]);

        Log::info('CHATBOT: sending response', [
            'text' => $formatted['text'] ?? null,
            'metadata' => $formatted['metadata'] ?? null
        ]);

    // return $this->send($conversation, $formatted);
    // $this->send($conversation, $formatted['text']);
    $sentMessages[] = $this->send(
    $conversation,
    $formatted['text'],
    $formatted['metadata'] ?? null
    );
    return $sentMessages;
  }

  protected function handleIntent($conversation, $message, $context)
  {
    Log::info('INTENT: matching', [
      'message' => $message
    ]);
    $intent = app(IntentMatcher::class)->match($message);
    Log::info('INTENT: result', [
      'intent' => $intent?->id
    ]);

    if(!$intent){

     // Fallback inteligente
     $products = app(ProductService::class)->searchForChatbot($message);

     if($products->isNotEmpty()){
       return [
         'text' => 'Encontré un producto relacionado con lo que buscas 👇',
         'metadata' => [
          'type' => 'products',
          'products' => $products->values()
         ]
       ];
     }

      // return 'No entendí, ¿puedes reformular?';
      return [
        'text' => 'Puedo ayudarte con productos LED, letreros, neón, pantallas y cotizaciones. ¿Qué necesitas exactamente?',
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
    // 'metadata' => $metadata
    'metadata' => !empty($metadata) ? $metadata : null
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
    $message = $conversation->messages()->create([
    'message_text' => $text,
    'sender' => 'bot',
    'metadata' => $metadata
    ]);
    $conversation->touch();

    $this->pruneMessages($conversation);

    // return $text;
    // return [
    //   'text' => is_array($text) ? $text['text'] : $text,
    //   'metadata' => $metadata
    // ];
    return $message;
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

    if(!cache()->add('chatbot_cleanup', true, 3600)){
      return;
    }

    // cache()->remember('chatbot_cleanup', 3600, function () {
    //   ChatbotConversation::where('updated_at', '<', now()->subDay())->delete();
    // });

    ChatbotConversation::where(
     'updated_at',
     '<',
     now()->subDay()
    )->delete();
  }

}
