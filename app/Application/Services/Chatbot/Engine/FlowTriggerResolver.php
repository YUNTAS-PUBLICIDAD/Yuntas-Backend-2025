<?php
namespace App\Application\Services\Chatbot\Engine;

use App\Models\ChatbotFlow;
use App\Models\ChatbotFlowTrigger;
use Illuminate\Support\Facades\Log;

class FlowTriggerResolver
{
  public function tryStart(string $message, $context)
  {
    // Si ya está en flujo -> NO hacer nada
    if(data_get($context->data, 'flow')){
      return;
    }

    $message = strtolower(trim($message));

    Log::info('FLOW TRIGGER: checking', ['message' => $message]);
    // Buscar trigger tipo keyword
    $trigger = ChatbotFlowTrigger::query()
    ->where('type', 'keyword')
    ->get()
    ->first(function ($t) use ($message) {
      // return $message === strtolower($t->value);
      $value = strtolower(trim($t->value));

      Log::info('TRIGGERS DEBUG', [
         'message' => $message,
         'trigger_value' => $t->value,
         'normalized' => $value,
         'match' => str_contains($message, $value)
       ]);

      return str_contains($message, $value);
    });


    if(!$trigger){
      Log::warning('FLOW TRIGGER: no match');
      return;
    }

    $flow = ChatbotFlow::find($trigger->flow_id);

    if(!$flow || !$flow->start_node_uuid){
      Log::error('FLOW TRIGGER: flow without start node', [
        'flow_id' => $trigger->flow_id
      ]);

      return;
    }

    // if(!$trigger) return;

    // Activar flujo
    data_set($context->data, 'flow', [
      // 'flow_id' => $trigger->flow_id,
      // 'node_id' => $trigger->node_uuid
      'flow_id' => $flow->id,
      'node_id' => $flow->start_node_uuid
    ]);
    Log::info('FLOW TRIGGER: started', [
       'flow_id' => $flow->id,
       'start_node' => $flow->start_node_uuid
     ]);

    return true;
  }
}
