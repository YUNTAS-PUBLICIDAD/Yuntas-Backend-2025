<?php
namespace App\Application\Services\Chatbot\Engine;

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
      return null;
    };

    // EXIT GLOBAL (antes de todo)
    if(strtolower(trim($message)) === 'salir'){
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

    // $finalResponse = null;
    $responses = [];

    // $node = $this->getNode($flowState['node_id']);

    // Log::info('FLOW: current node', [
    //   'node_id' => $flowState['node_id'],
    //   'node' => $node
    // ]);

    // if(!$node){
    //   Log::error('FLOW: node not found');
    //   return null;


    while($currentNodeId){
      $node = $this->getNode($currentNodeId);

      if(!$node) break;

      $input = $isFirstIteration ? $message : null;

      // $result = $this->resolveNode($node, $message);
      $result = $this->resolveNode($node, $input);

      // if($result){
      //   $responses[] = $result;
      // }

      if(!$result) break;

      $responses[] = $result;

      // // Guardamos la última respuesta válida
      // $finalResponse = $result;

      // Avanzar
      if(!empty($result['next'])){
        $currentNodeId = $result['next'];
        data_set($context->data, 'flow.node_id', $currentNodeId);
      }else {
        break;
      }

      // CLAVE: detener si es menú
      if($node['type'] === 'menu'){
        break;
      }

      // después del primer nodo, ya no usamos el mensaje del usuario
      // $message = '';
      $isFirstIteration = false;
    }

    // return $finalResponse;
    return $responses;
  }

  // ─────────────────────────────
  // BOOT FLOW (cache + index)
  // ─────────────────────────────
  protected function boot($flowId)
  {
    if ($this->flow) return;
    Log::info('FLOW: booting', ['flow_id' => $flowId]);

    $nodes = DB::table('chatbot_flow_nodes')
    ->where('flow_id', $flowId)
    ->get();

    $edges = DB::table('chatbot_flow_edges')
    ->where('flow_id', $flowId)
    ->get();

    Log::info('FLOW: data loaded', [
      'nodes_count' => $nodes->count(),
      'edges_count' => $edges->count()
    ]);

    foreach($nodes as $n){
      $this->nodeMap[$n->uuid] = [
        'id' => $n->uuid,
        'type' => $n->type,
        'message' => $n->message,
        'options' => $n->options ? json_decode($n->options, true): []
      ];
    }

    foreach ($edges as $e){
      $this->edgeMap[$e->from_uuid][] = [
        'target' => $e->to_uuid,
        'sourceHandle' => $e->source_handle
      ];
    }

    // $raw = DB::table('chatbot_flows')
    //   ->where('id', $flowId)
    //   ->value('graph');

    // $this->flow = is_array($raw) ? $raw : json_decode($raw, true);

    // // index nodos
    // foreach ($this->flow['nodes'] ?? [] as $n) {
    //   $this->nodeMap[$n['id']] = $n;
    // }

    // // index edges por source
    // foreach ($this->flow['edges'] ?? [] as $e) {
    //   $this->edgeMap[$e['source']][] = $e;
    // }
  }

  // ─────────────────────────────
  protected function getNode($nodeId)
  {
    $node = $this->nodeMap[$nodeId] ?? null;

    if (!$node) return null;

    return [
      'id' => $node['id'],
      // 'type' => $node['data']['type'] ?? 'message',
      // 'message' => $node['data']['message'] ?? '',
      // 'options' => $node['data']['options'] ?? [],
      'type' => $node['type'] ?? 'message',
      'message' => $node['message'] ?? '',
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

        // $next = $this->resolveMenu($node, $message);

        // if(!$next){
        //   return [
        //     'text' => "No entendí esa opción. Elige una de la lista",
        //     'metadata' => [
        //       'type' => 'options',
        //       'options' => $node['options']
        //     ],
        //     'next' => null
        //   ];
        // }

        // return [
        //   'text' => $node['message'],
        //   'metadata' => [
        //     'type' => 'options',
        //     'options' => $node['options']
        //   ],
        //   'next' => $next
        // ];

        $result = $this->resolveMenu($node, $message);

        Log::info('FLOW: node resolved', [
          'result' => $result
        ]);

          if(!$result){
            return [
              'text' => "No entendí esa opción. Elige una de la lista",
              'metadata' => [
                'type' => 'options',
                'options' => $node['options']
              ],
              'next' => null
            ];
          }

          return $result;

      case 'action':
        // simple por ahora
        return [
          'text' => '',
          'metadata' => null,
          'next' => $this->nextFromEdge($node['id'])
        ];
    default:
      return [
        'text' => '',
        'metadata' => null,
        'next' => null
      ];
    }

    // return null;
  }

  // ─────────────────────────────
  // EDGE NORMAL (message → siguiente)
  // ─────────────────────────────
  protected function nextFromEdge($nodeId)
  {
    $edges = $this->edgeMap[$nodeId] ?? [];

    return $edges[0]['target'] ?? null;
  }

  // ─────────────────────────────
  // MENU → usa sourceHandle (clave real)
  // ─────────────────────────────
  protected function resolveMenu($node, $message)
  {
    $edges = $this->edgeMap[$node['id']] ?? [];

    // MODO RENDER (sin input)
    if(!$message){
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
       'options' => $node['options'],
       'edges' => $edges
     ]);

     // MODO INTERACCIÓN
    foreach ($node['options'] as $opt) {

      Log::info('FLOW: checking option', [
           'label' => $opt['label'],
           'id' => $opt['id']
         ]);


      // match básico por texto
      // if (strtolower(trim($opt['label'])) !== strtolower(trim($message))) {
      //   continue;
      // }

      if(!str_contains(strtolower($message), strtolower($opt['label']))){
        continue;
      }
      Log::info('FLOW: option matched', $opt);
      // buscar edge que sale de ese handle
      // foreach ($edges as $edge) {
      //   if (($edge['sourceHandle'] ?? null) === $opt['id']) {
      //     return $edge['target'];
      //   }
      // }

      // 🚀 CASOS ESPECIALES (links, whatsapp, etc)
          if (($opt['type'] ?? null) === 'url' || ($opt['type'] ?? null) === 'link') {
            return [
              'text' => 'Abriendo enlace...',
              'metadata' => [
                'type' => 'url',
                'url' => $opt['value'] ?? null
              ],
              'next' => null
            ];
          }

          if (($opt['type'] ?? null) === 'whatsapp') {
            return [
              'text' => 'Redirigiendo a WhatsApp...',
              'metadata' => [
                'type' => 'whatsapp',
                'url' => $opt['value'] ?? null
              ],
              'next' => null
            ];
          }

          // 🔗 flujo normal con edges
          foreach ($edges as $edge) {
             Log::info('FLOW: checking edge', $edge);
            if (($edge['sourceHandle'] ?? null) === $opt['id']) {

              Log::info('FLOW: edge matched', [
                       'target' => $edge['target']
                     ]);
              return [
                'text' => $node['message'],
                'metadata' => [
                  'type' => 'options',
                  'options' => $node['options']
                ],
                'next' => $edge['target']
              ];
            }
          }
          Log::warning('FLOW: option matched but no edge found', [
               'option_id' => $opt['id']
             ]);
    }

      Log::warning('FLOW: no option matched');

    return null;
  }
}
