<?php

namespace App\Application\Services\Chatbot\Actions;

use App\Application\Services\Chatbot\MessageParser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ActionExecutor
{

  public function execute($intent, $answer, $conversation, $message, $context)
 {
   $actions = collect()
    ->merge($intent->actions)
    ->merge($answer->actions)
    ->sortBy('pivot.priority');

    $metadata = [];

    foreach ($actions as $action){
      if(!app(ConditionEvaluator::class)->evaluate($action, $conversation, $message)){
        continue;
      }
      match($action->action_type){
        'update_context' => $this->update($action, $context, $message),
        'call_n8n' => $this->callN8N($action, $conversation),
        'send_metadata' => $metadata[] = $action->parameters,
        default => null,
      };
    }
    return $metadata;
 }


  protected function update($action, $context, $message)
  {
    $params = $action->parameters ?? [];

    if(!isset($params['key'], $params['value'])) return;

    $value = match ($params['value']){
      '__name__' => app(MessageParser::class)->extractName($message),
      '__phone__' => app(MessageParser::class)->extractPhone($message),
      default => $params['value']
    };
    if(!$value) return;
    data_set($context->data, $params['key'], $value);
  }

  protected function callN8N($action, $conversation)
  {
    if(!config('chatbot.n8n_enabled')) return;

    Http::post($action->parameters['webhook_url'], [
    'conversation_id' => $conversation->id
    ]);
  }
}
