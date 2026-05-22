<?php
namespace App\Application\Services\Chatbot\Engine;

use App\Application\Services\Product\ProductDetector;
use App\Models\ChatbotFlow;
use App\Models\ChatbotFlowTrigger;
use Illuminate\Support\Facades\Log;

class FlowTriggerResolver
{
  public function tryStart(string $message, $context)
  {
  //   // Si ya está en flujo -> NO hacer nada
  //   if(data_get($context->data, 'flow')){
  //     return;
  //   }

  //   $message = strtolower(trim($message));

  //   Log::info('FLOW TRIGGER: checking', ['message' => $message]);
  //   // Buscar trigger tipo keyword
  //   $trigger = ChatbotFlowTrigger::query()
  //   ->where('type', 'keyword')
  //   ->get()
  //   ->first(function ($t) use ($message) {
  //     // return $message === strtolower($t->value);
  //     $value = strtolower(trim($t->value));

  //     Log::info('TRIGGERS DEBUG', [
  //        'message' => $message,
  //        'trigger_value' => $t->value,
  //        'normalized' => $value,
  //        'match' => str_contains($message, $value)
  //      ]);

  //     return str_contains($message, $value);
  //   });


  //   if(!$trigger){
  //     Log::warning('FLOW TRIGGER: no match');
  //     return;
  //   }

  //   $flow = ChatbotFlow::find($trigger->flow_id);

  //   if(!$flow || !$flow->start_node_uuid){
  //     Log::error('FLOW TRIGGER: flow without start node', [
  //       'flow_id' => $trigger->flow_id
  //     ]);

  //     return;
  //   }

  //   // if(!$trigger) return;

  //   // Activar flujo
  //   data_set($context->data, 'flow', [
  //     // 'flow_id' => $trigger->flow_id,
  //     // 'node_id' => $trigger->node_uuid
  //     'flow_id' => $flow->id,
  //     'node_id' => $flow->start_node_uuid
  //   ]);
  //   Log::info('FLOW TRIGGER: started', [
  //      'flow_id' => $flow->id,
  //      'start_node' => $flow->start_node_uuid
  //    ]);

  //   return true;

  if(data_get($context->data, 'flow.active')){
         return false;
     }

     Log::info('PRODUCT FLOW: detecting', [
      'message' => $message
     ]);

     // 1. PRODUCT DISCOVERY FLOW
     $product = app(ProductDetector::class)
         ->detect($message);

         Log::info('PRODUCT FLOW: detector result', [
            'product' => $product?->name
         ]);

     if($product){

         data_set($context->data, 'flow', [
             'active' => true,
             'type' => 'conversational',
             'name' => 'product_discovery',
             'step' => 'installation'
         ]);

         data_set($context->data, 'entities', [
             'product_id' => $product->id,
             'product_name' => $product->name,
             'product_slug' => $product->slug
         ]);

         return true;
     }

     // 2. VISUAL FLOWS
     return $this->startVisualFlow(
         $message,
         $context
     );
  }


  protected function startVisualFlow(
      string $message,
      $context
  )
  {
      $message = strtolower(trim($message));

      $trigger = ChatbotFlowTrigger::query()
          ->where('type', 'keyword')
          ->get()
          ->first(function ($t) use ($message) {

              $value = strtolower(trim($t->value));

              return str_contains($message, $value);
          });

      if(!$trigger){
          return false;
      }

      $flow = ChatbotFlow::find($trigger->flow_id);

      if(!$flow || !$flow->start_node_uuid){
          return false;
      }

      data_set($context->data, 'flow', [
          'active' => true,
          'type' => 'visual',
          'flow_id' => $flow->id,
          'node_id' => $flow->start_node_uuid
      ]);

      return true;
  }
}
