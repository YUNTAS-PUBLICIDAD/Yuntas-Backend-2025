<?php

namespace App\Application\Services\Chatbot;

use Http;
use Log;

class ActionExecutor
{
  public function execute($actions, $conversation, $message)
  {
    foreach ($actions as $action) {
      if (!$this->shouldRun($action, $conversation, $message)) {
        continue;
      }
      match ($action->action_type) {
        // 'call_n8n' => $this->callN8N($action, $conversation),
        'update_context'=> $this->updateContext($action, $conversation),
        'log' => $this->logAction($action, $conversation),
        'call_n8n' => $this->skipN8N($action),
         default => null
      };
    }
  }

  protected function shouldRun($action, $conversation, $message)
  {
    return app(ConditionEvaluator::class)
    ->evaluate($action, $conversation, $message);
  }

  protected function callN8N($action, $conversation)
  {
    $params = $action->parameters;
    Http::post($params['webhook_url'], [
      'conversation_id' => $conversation->id,
      'context' => $conversation->context
    ]);
  }

  protected function logAction($action, $conversation)
  {
      Log::info('Chatbot action executed', [
        'action_id' => $action->id,
        'conversation_id' => $conversation->id
      ]);
  }

  protected function skipN8N($action)
  {
    Log::info('N8N action skipped', [
      'action_id' => $action->id,
      'name' => $action->name
    ]);
  }

  protected function updateContext($action, $conversation)
  {
    $params = $action->parameters;
    $context = $conversation->context ?? [];
    $context[$params['key']] = $params['value'];
    $conversation->update([
      'context' => $context
    ]);
  }
}