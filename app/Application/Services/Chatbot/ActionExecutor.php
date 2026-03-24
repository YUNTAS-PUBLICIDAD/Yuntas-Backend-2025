<?php

namespace App\Application\Services\Chatbot;

use Http;
use Log;

class ActionExecutor
{
  // Ejecuta SOLO acciones ya filtradas
  public function execute($actions, $conversation, $message)
  {
    foreach ($actions as $action) {
      // if (!$this->shouldRun($action, $conversation, $message)) {
      //   continue;
      // }
      match ($action->action_type) {
        // 'call_n8n' => $this->callN8N($action, $conversation),
        'update_context'=> $this->updateContext($action, $conversation),
        'log' => $this->logAction($action, $conversation),
        // 'call_n8n' => $this->skipN8N($action),
        'call_n8n' => $this->handleN8N($action, $conversation),
         default => null
      };
    }
  }

  // Filtra acciones que cumplen condiciones
  public function filterExecutable($actions, $conversation, $message)
  {
    return collect($actions)->filter(function ($action) use ($conversation, $message){
      return $this->shouldRun($action, $conversation, $message);
    });
  }

  // Evalúa condiciones
  protected function shouldRun($action, $conversation, $message)
  {
    return app(ConditionEvaluator::class)
    ->evaluate($action, $conversation, $message);
  }

  // Manejo centralizado de N8N
  protected function handleN8N($action, $conversation)
  {
    if (config('chatbot.n8n_enabled')) {
      $this->callN8N($action, $conversation);
    }else {
      $this->skipN8N($action);
    }
  }

  // Llamanda real a N8N
  protected function callN8N($action, $conversation)
  {
    // $params = $action->parameters;
    $params = $action->parameters ?? [];
    if (!isset($params['webhook_url'])) {
      return;
    }
    Http::post($params['webhook_url'], [
      'conversation_id' => $conversation->id,
      'context' => $conversation->context
    ]);
  }

  
  // Simulación (cuando N8N está desactivado)
  protected function skipN8N($action)
  {
    Log::info('N8N action skipped', [
      'action_id' => $action->id,
      'name' => $action->name
      ]);
      }

      // Loggin de acciones
      protected function logAction($action, $conversation)
      {
          Log::info('Chatbot action executed', [
            'action_id' => $action->id,
            'conversation_id' => $conversation->id
          ]);
      }

  // Actualiza contexto de forma segura
  protected function updateContext($action, $conversation)
  {
    // $params = $action->parameters;
    $params = $action->parameters ?? [];
    if (!isset($params['key'], $params['value'])) {
    return;
    }
    $context = $conversation->context ?? [];
    // $context[$params['key']] = $params['value'];
    // Permite Keys tipo: context.user.name
    data_set($context, $params['key'], $params['value']);

    $conversation->update([
      'context' => $context
    ]);
  }
}