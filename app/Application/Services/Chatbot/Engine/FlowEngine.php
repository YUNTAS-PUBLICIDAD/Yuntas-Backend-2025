<?php

namespace App\Application\Services\Chatbot\Engine;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlowEngine
{
    protected ?array $flow = null;

    protected array $nodeMap = [];

    protected array $edgeMap = [];

    public function handle($conversation, string $message, $context)
    {
        Log::info('FLOW: handle start', [
            'message' => $message,
            'flow' => data_get($context->data, 'flow')
        ]);

        $flowState = data_get($context->data, 'flow');

        if (!$flowState) {
            Log::warning('FLOW: no flow state');

            return [];
        }

        // EXIT GLOBAL
        if (strtolower(trim($message)) === 'salir') {

            data_set($context->data, 'flow', null);

            return [[
                'text' => 'Saliste del flujo',
                'metadata' => null,
                'next' => null
            ]];
        }

        $this->boot($flowState['flow_id']);

        $currentNodeId = $flowState['node_id'];

        $isFirstIteration = true;

        $responses = [];

        while ($currentNodeId) {

            $node = $this->getNode($currentNodeId);

            if (!$node) {

                Log::warning('FLOW: node not found', [
                    'node_id' => $currentNodeId
                ]);

                data_set($context->data, 'flow', null);

                break;
            }

            // SOLO el primer nodo recibe input usuario
            $input = $isFirstIteration
                ? $message
                : null;

            $result = $this->resolveNode($node, $input);

            Log::info('FLOW: node resolved', [
                'node_id' => $node['id'],
                'node_type' => $node['type'],
                'result' => $result
            ]);

            // 🚨 IMPORTANTE:
            // si el flow NO entendió input
            // delegamos al intent engine
            if (!$result) {
                // Liberar flow
                // data_set($context->data, 'flow', null);
                // return [];
                break;
            }

            // evitar mensajes vacíos
            if (
                !empty($result['text']) ||
                !empty($result['metadata'])
            ) {
                $responses[] = $result;
            }

            // avanzaer
            $nextNodeId = $result['next'] ?? null;

            if(!$nextNodeId){
              data_set($context->data, 'flow', null);

              break;
            }

            $currentNodeId = $nextNodeId;

            data_set(
            $context->data,
            'flow.node_id',
            $currentNodeId
            );

            // avanzar nodo
            // if (!empty($result['next'])) {

            //     $currentNodeId = $result['next'];

            //     data_set(
            //         $context->data,
            //         'flow.node_id',
            //         $currentNodeId
            //     );
            // } else {

            //     // FIN FLOW
            //     data_set($context->data, 'flow', null);

            //     break;
            // }

            // detener render en menú
            // para esperar interacción usuario
            // if (
            //     $node['type'] === 'menu'
            //     && !$input
            // ) {
            //     break;
            // }

            // $isFirstIteration = false;

            // auto-render siguiente menú
            $nextNode = $this->getNode($currentNodeId);

            // if($nextNode && $nextNode['type'] === 'menu'){
            //   $menuResult = $this->resolveMenu(
            //   $nextNode,
            //   null
            //   );

            //   if($menuResult){
            //     $responses[] = $menuResult;

            //     data_set(
            //     $context->data,
            //     'flow.node_id',
            //     $nextNode['id']
            //     );
            //   }
            //   break;
            // }

            if($nextNode && $this->isInteractiveNode($nextNode)){
              Log::info('FLOW: auto rendering interactive node', [
                'node_id' => $nextNode['id'],
                'type' => $nextNode['type']
              ]);
             // $interactiveResult = $this->resolveMenu(
             // $nextNode,
             // null
             // );
             $interactiveResult = $this->resolveNode(
             $nextNode,
             null
             );
             Log::info('FLOW: interactive result', [
              'result' => $interactiveResult
             ]);

             if($interactiveResult){
               $responses[] = $interactiveResult;
             }
             break;
            }
            $isFirstIteration = false;
        }

        return $responses;
    }

    // ─────────────────────────────
    // BOOT
    // ─────────────────────────────
    protected function boot($flowId)
    {
        if ($this->flow) {
            return;
        }

        Log::info('FLOW: booting', [
            'flow_id' => $flowId
        ]);

        $nodes = DB::table('chatbot_flow_nodes')
            ->where('flow_id', $flowId)
            ->get();

        $edges = DB::table('chatbot_flow_edges')
            ->where('flow_id', $flowId)
            ->get();

        foreach ($nodes as $n) {

            $this->nodeMap[$n->uuid] = [
                'id' => $n->uuid,
                'type' => $n->type,
                'message' => $n->message,
                'metadata' => $n->metadata ? json_decode($n->metadata, true) : [],
                'options' => $n->options
                    ? json_decode($n->options, true)
                    : []
            ];
        }

        foreach ($edges as $e) {

            $this->edgeMap[$e->from_uuid][] = [
                'target' => $e->to_uuid,
                'sourceHandle' => $e->source_handle
            ];
        }

        $this->flow = [
            'loaded' => true
        ];
    }

    // ─────────────────────────────
    protected function getNode($nodeId)
    {
        $node = $this->nodeMap[$nodeId] ?? null;

        if (!$node) {
            return null;
        }

        return [
            'id' => $node['id'],
            'type' => $node['type'] ?? 'message',
            'message' => $node['message'] ?? '',
            'metadata' => $node['metadata'] ?? [],
            'options' => $node['options'] ?? []
        ];
    }

    // ─────────────────────────────
    protected function resolveNode($node, $message)
    {
        switch ($node['type']) {

            case 'message':

                return [
                    'text' => $node['message'],
                    'metadata' => null,
                    'next' => $this->nextFromEdge($node['id'])
                ];

            case 'menu':

                return $this->resolveMenu(
                    $node,
                    $message
                );

            case 'action':

            $option = $node['options'][0] ?? null;

            if(!$option){
              return null;
            }

            // Whatsapp
            if(($option['type'] ?? null) === 'whatsapp'){
              return [
                'text' => $node['message'],
                'metadata' => [
                  'type' => 'whatsapp',
                  'whatsapp_url' => $option['value']
                ],
                'next' => null
              ];
            }

            // URL
            if(($option['type'] ?? null) === 'url' || ($option['type'] ?? null) === 'link'){
              return [
                  'text' => $node['message'],
                  'metadata' => [
                    'type' => 'url',
                    'url' => $option['value']
                  ],
                  'next' => null
              ];
            }

            return [
              'text' => $node['message'],
              'metadata' => null,
              'next' => null
            ];

                // return [
                //     'text' => '',
                //     'metadata' => null,
                //     'next' => $this->nextFromEdge($node['id'])
                // ];

            case 'catalog':
              return $this->resolveCatalog(
                $node,
                $message
              );

            default:

                return null;
        }
    }

    // ─────────────────────────────
    protected function nextFromEdge($nodeId)
    {
        $edges = $this->edgeMap[$nodeId] ?? [];

        return $edges[0]['target'] ?? null;
    }

    // ─────────────────────────────
    protected function resolveMenu($node, $message)
    {
        $edges = $this->edgeMap[$node['id']] ?? [];

        // MODO RENDER
        if (!$message) {

            return [
                'text' => $node['message'],
                'metadata' => [
                    'type' => 'options',
                    'options' => $node['options']
                ],
                'next' => null
            ];
        }

        Log::info('FLOW: resolveMenu start', [
            'message' => $message,
            'options' => $node['options']
        ]);

        // MODO INTERACCIÓN
        foreach ($node['options'] as $opt) {

          if(!str_starts_with($message, '__option__:'))
          {
            continue;
          }

          $selectedOptionId = str_replace(
            '__option__:',
            '',
            $message
          );

          if($selectedOptionId !== $opt['id']){
            continue;
          }

            // $optionLabel = strtolower(
            //     trim($opt['label'])
            // );

            // $userMessage = strtolower(
            //     trim($message)
            // );

            // 🚨 MATCH EXACTO
            // if ($userMessage !== $optionLabel) {
            //     continue;
            // }

            // if(
            //   !str_contains($optionLabel, $userMessage) && !str_contains($userMessage, $optionLabel)
            // ){
            //   continue;
            // }

            Log::info('FLOW: option matched', [
                'option' => $opt
            ]);

            // URL
            // if (
            //     ($opt['type'] ?? null) === 'url' ||
            //     ($opt['type'] ?? null) === 'link'
            // ) {

            //     return [
            //         'text' => 'Abriendo enlace...',
            //         'metadata' => [
            //             'type' => 'url',
            //             'url' => $opt['value'] ?? null
            //         ],
            //         'next' => null
            //     ];
            // }

            // // WHATSAPP
            // if (
            //     ($opt['type'] ?? null) === 'whatsapp'
            // ) {

            //     return [
            //         'text' => 'Redirigiendo a WhatsApp...',
            //         'metadata' => [
            //             'type' => 'whatsapp',
            //             'url' => $opt['value'] ?? null
            //         ],
            //         'next' => null
            //     ];
            // }

            // FLOW EDGE
            foreach ($edges as $edge) {

              Log::info('FLOW EDGE CHECK', [
                 'edge_source_handle' => $edge['sourceHandle'] ?? null,
                 'option_id' => $opt['id']
              ]);

                if (
                    ($edge['sourceHandle'] ?? null)
                    ===
                    $opt['id']
                ) {

                    return [
                        // 🚨 NO re-renderizar menú
                        'text' => '',
                        'metadata' => null,
                        'next' => $edge['target']
                    ];
                }
            }
        }

        // Log::warning('FLOW: no option matched');

        // 🚨 CLAVE:
        // devolver null para delegar al intent engine
        // return null;

        // return [
        // 'text' => 'No entendí esa opción.',
        //     'metadata' => [
        //         'type' => 'options',
        //         'options' => $node['options']
        //     ],
        //     'next' => $node['id']
        // ];

        Log::warning('FLOW: option without edge');

        return [
            'text' => null,
            'metadata' => null,
            'next' => null
        ];
    }

    protected function resolveCatalog($node, $message)
    {
      $metadata = $node['metadata'] ?? [];

          $categoryId = $metadata['category_id'] ?? null;

          // ─────────────────────
          // VALIDACIÓN
          // ─────────────────────
          if (!$categoryId) {

              return [
                  'text' => 'Categoría no configurada.',
                  'metadata' => null,
                  'next' => null
              ];
          }

          // ─────────────────────
          // PRODUCTOS DIRECTOS
          // ─────────────────────
          $products = Product::query()
              ->whereHas('categories', function ($q) use ($categoryId) {
                  $q->where('categories.id', $categoryId);
              })
              ->take(6)
              ->get();

          if ($products->isEmpty()) {

              return [
                  'text' => 'No encontré productos en esta categoría.',
                  'metadata' => null,
                  'next' => null
              ];
          }

          return [
              'text' => $node['message'],

              'metadata' => [
                  'type' => 'products',

                  'products' => $products
                      ->map(fn ($p) => [
                          'id' => $p->id,
                          'name' => $p->name,
                          'slug' => $p->slug,
                          'price' => $p->price,
                          'image' => $p->image
                      ])
                      ->values()
                      ->toArray()
              ],

              'next' => null
          ];
    }

    protected function isInteractiveNode($node)
    {
      return in_array($node['type'], [
        'menu',
        // 'catalog'
      ]);
    }


}
