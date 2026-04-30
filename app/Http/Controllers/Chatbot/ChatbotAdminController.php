<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\ChatbotFlow;
use App\Models\ChatbotFlowEdge;
use App\Models\ChatbotFlowNode;
use App\Models\ChatbotFlowTrigger;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatbotAdminController extends Controller
{
    // =====================
    // FLOWS CRUD
    // =====================

    public function index()
    {
        return ChatbotFlow::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return ChatbotFlow::create($data);
    }

    public function show($id)
    {
        return ChatbotFlow::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $flow = ChatbotFlow::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $flow->update($data);
        return $flow;
    }

    public function destroy($id)
    {
        ChatbotFlow::findOrFail($id)->delete();
        return response()->noContent();
    }

    // =====================
    // LOAD GRAPH
    // =====================

    public function getGraph($id)
    {

        $flow = ChatbotFlow::findOrFail($id);
        $nodes = ChatbotFlowNode::where('flow_id', $id)->get();
        $edges = ChatbotFlowEdge::where('flow_id', $id)->get();
        $triggers = ChatbotFlowTrigger::where('flow_id', $id)->get();

        return response()->json([
            'name' => $flow->name,
            'start_node_uuid' => $flow->start_node_uuid,
            'nodes' => $nodes->map(fn($n) => [
                'id'       => $n->uuid,
                'type'     => $n->type,
                'position' => $this->ensurePosition($n->position),
                'data'     => [
                    'type' => $n->type ?? 'message',
                    'message'  => $n->message ?? '',
                    'metadata' => $n->metadata ? (object) $n->metadata : (object) [],
                    'options'  => $n->options ?? [],
                ],
            ]),
            'edges' => $edges->map(fn($e) => [
                'id'     => $e->uuid,
                'source' => $e->from_uuid,
                'target' => $e->to_uuid,
                'label'  => $e->label ?? '',
                'sourceHandle' => $e->source_handle,
            ]),
            // Triggers ya no definen nodo (pero devolvemos por compatibilidad)
            'triggers' => $triggers->map(fn($t) => [
              'id' => $t->id,
              'type' => $t->type,
              'value' => $t->value,
              // 'node_id' => $t->node_uuid
            ]),
        ]);
    }

    // ─────────────────────────────────────────
        // Normaliza la posición a siempre devolver
        // un stdClass serializable como JSON objeto.
        //
        // Casos cubiertos:
        //   null / vacío              → {x:0, y:0}
        //   array ['x'=>1, 'y'=>2]    → {x:1, y:2}
        //   stdClass {x:1, y:2}       → {x:1, y:2}
        //   string JSON '{"x":1}'     → {x:1, y:0}
        // ─────────────────────────────────────────

    // =====================
    // POSITION NORMALIZER
    // =====================
    private function ensurePosition(mixed $raw): object
    {
      if(empty($raw)){
        return (object) ['x' => 0, 'y' => 0];
      }

      // Cast PHP array (viene del model con cast 'array')
      if(is_array($raw)){
        return (object)[
          'x' => (float) ($raw['x'] ?? 0),
          'y' => (float) ($raw['y'] ?? 0),
        ];
      }

      // stdClass (viene del model con cast 'object')
      if(is_object($raw)){
        return (object)[
          'x' => (float) ($raw->x ?? 0),
          'y' => (float) ($raw->y ?? 0)
        ];
      }

      // String JSON como fallback
      if(is_string($raw)){
        $decoded = json_decode($raw, true);
        if(is_array($decoded)) {
          return (object)[
            'x' => (float) ($decoded['x'] ?? 0),
            'y' => (float) ($decoded['y'] ?? 0),
          ];
        }
      }
      return (object) ['x' => 0, 'y' => 0];
    }

    // =====================
    // SAVE GRAPH
    // =====================

    public function saveGraph(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $flow = ChatbotFlow::findOrFail($id);

            $payload = $request->all();

            // DEBUG (haz esto una vez)
            logger()->info('GRAPH PAYLOAD', $payload);

            $nodes = $payload['nodes'] ?? [];
            $edges = $payload['edges'] ?? [];
            $triggers = $payload['triggers'] ?? [];
            $startNode = $payload['start_node_uuid'] ?? null;

            // =====================
            // VALIDACIONES SERIAS
            // =====================
            if(!$startNode){
              throw new Exception('El flow debe tener un nodo inicial');
            }

            $nodeIds = collect($nodes)->pluck('id');

            if(!$nodeIds->contains($startNode)){
              throw new Exception('El nodo inicial no existe en el flow');
            }

            // =====================
            // UPDATE FLOW
            // =====================
            $flow->update([
              'name' => $payload['name'] ?? $flow->name,
              'start_node_uuid' => $startNode
              // 'start_node_uuid' => $payload['start_node_uuid'] ?? null
            ]);

            // =====================
            // RESET
            // =====================
            // 🔴 LIMPIAR TODO (simple y seguro)
            ChatbotFlowNode::where('flow_id', $id)->delete();
            ChatbotFlowEdge::where('flow_id', $id)->delete();
            ChatbotFlowTrigger::where('flow_id', $id)->delete();

            // 🟢 INSERT NODES
            foreach ($nodes as $node) {
                ChatbotFlowNode::create([
                    'uuid'    => $node['id'],
                    'flow_id' => $id,
                    'type'    => $node['data']['type'] ?? 'message',
                    'position'=> $node['position'] ?? ['x'=>0,'y'=>0],
                    'message' => $node['data']['message'] ?? '',
                    'metadata'=> $node['data']['metadata'] ?? [],
                    'options' => $node['data']['options'] ?? [],
                ]);
            }

            // 🟢 INSERT EDGES
            foreach ($edges as $edge) {
                ChatbotFlowEdge::create([
                    'uuid'      => $edge['id'],
                    'flow_id'   => $id,
                    'from_uuid' => $edge['source'],
                    'to_uuid'   => $edge['target'],
                    'label'     => $edge['label'] ?? '',
                    'source_handle' => $edge['sourceHandle'] ?? null,
                ]);
            }

            // TRIGGERS
            foreach ($triggers as $trigger){
              ChatbotFlowTrigger::create([
                'flow_id' => $id,
                'type' => $trigger['type'], // Keyword | intent | event
                'value' => $trigger['value'], // "hola"
                // 'node_uuid' => $trigger['node_id'] // start node
                'node_uuid' => null
              ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'ok',
                'nodes'  => count($nodes),
                'edges'  => count($edges),
                'triggers' => count($triggers)
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
