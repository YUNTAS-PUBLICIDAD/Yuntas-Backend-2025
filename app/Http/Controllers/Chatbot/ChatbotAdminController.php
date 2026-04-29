<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\ChatbotFlow;
use App\Models\ChatbotFlowEdge;
use App\Models\ChatbotFlowNode;
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
        $nodes = ChatbotFlowNode::where('flow_id', $id)->get();
        $edges = ChatbotFlowEdge::where('flow_id', $id)->get();

        return response()->json([
            'nodes' => $nodes->map(fn($n) => [
                'id'       => $n->uuid,
                'type'     => $n->type,
                'position' => $n->position,
                'data'     => [
                    'message'  => $n->message,
                    'metadata' => $n->metadata,
                    'options'  => $n->options,
                ],
            ]),
            'edges' => $edges->map(fn($e) => [
                'id'     => $e->uuid,
                'source' => $e->from_uuid,
                'target' => $e->to_uuid,
                'label'  => $e->label,
            ]),
        ]);
    }

    // =====================
    // SAVE GRAPH
    // =====================

    public function saveGraph(Request $request, $id)
    {
        // ─────────────────────────────────────────
        // VALIDACIÓN
        // ─────────────────────────────────────────
        $data = $request->validate([
            'nodes'          => 'required|array',
            'nodes.*.id'     => 'required|string',
            'edges'          => 'present|array',   // present pero puede ser vacío
            'edges.*.id'     => 'required_with:edges.*|string',
            'edges.*.source' => 'required_with:edges.*|string',
            'edges.*.target' => 'required_with:edges.*|string',
        ]);

        $nodes = $data['nodes'];
        $edges = $data['edges'] ?? [];

        // ─────────────────────────────────────────
        // BUG FIX #1 (backend):
        // Normalizamos los UUIDs entrantes antes de comparar.
        // El frontend puede mandar IDs con prefijo "reactflow__edge-"
        // o strings compuestos — limpiamos para evitar duplicados.
        //
        // Regla: si el id del edge contiene "--" lo recortamos a
        // "edge-{source}-{target}" para tener una clave estable.
        // ─────────────────────────────────────────
        $edges = collect($edges)->map(function ($edge) {
            if (
                isset($edge['id']) &&
                str_starts_with($edge['id'], 'reactflow__edge')
            ) {
                $edge['id'] = 'edge-' . ($edge['source'] ?? '') . '-' . ($edge['target'] ?? '');
            }
            return $edge;
        })->unique('id')->values()->toArray();

        // Colecciones de UUIDs que llegan del frontend
        $incomingNodeUuids = collect($nodes)->pluck('id')->filter()->values();
        $incomingEdgeUuids = collect($edges)->pluck('id')->filter()->values();

        DB::beginTransaction();

        try {
            // ─────────────────────────────────────────
            // PROTECCIÓN: flow debe existir
            // ─────────────────────────────────────────
            $flow = ChatbotFlow::where('id', $id)->firstOrFail();

            // ─────────────────────────────────────────
            // BUG FIX #1 (delete seguro):
            // Antes, si $incomingEdgeUuids estaba vacío se
            // saltaba el delete y no se borraban edges huérfanos.
            // Ahora borramos SIEMPRE los que no están en la lista.
            // Si la lista está vacía → borramos TODOS los edges del flow.
            // ─────────────────────────────────────────
            if ($incomingNodeUuids->isNotEmpty()) {
                ChatbotFlowNode::where('flow_id', $id)
                    ->whereNotIn('uuid', $incomingNodeUuids)
                    ->delete();
            } else {
                // Sin nodos entrantes → borrar todo (edge case raro pero seguro)
                ChatbotFlowNode::where('flow_id', $id)->delete();
            }

            if ($incomingEdgeUuids->isNotEmpty()) {
                ChatbotFlowEdge::where('flow_id', $id)
                    ->whereNotIn('uuid', $incomingEdgeUuids)
                    ->delete();
            } else {
                // Sin edges → limpiar todos los del flow (estado válido)
                ChatbotFlowEdge::where('flow_id', $id)->delete();
            }

            // ─────────────────────────────────────────
            // UPSERT NODES
            // ─────────────────────────────────────────
            foreach ($nodes as $node) {
                if (empty($node['id'])) continue;

                ChatbotFlowNode::updateOrCreate(
                    [
                        'uuid'    => $node['id'],
                        'flow_id' => $id,
                    ],
                    [
                        'type'     => $node['type']              ?? 'custom',
                        'position' => $node['position']          ?? ['x' => 0, 'y' => 0],
                        'message'  => $node['data']['message']   ?? '',
                        'metadata' => $node['data']['metadata']  ?? [],
                        'options'  => $node['data']['options']   ?? [],
                    ]
                );
            }

            // ─────────────────────────────────────────
            // UPSERT EDGES
            // ─────────────────────────────────────────
            foreach ($edges as $edge) {
                if (empty($edge['id'])) continue;

                ChatbotFlowEdge::updateOrCreate(
                    [
                        'uuid'    => $edge['id'],
                        'flow_id' => $id,
                    ],
                    [
                        'from_uuid' => $edge['source'] ?? null,
                        'to_uuid'   => $edge['target'] ?? null,
                        'label'     => $edge['label']  ?? '',
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status'  => 'ok',
                'message' => 'Graph saved successfully',
                'stats'   => [
                    'nodes' => count($nodes),
                    'edges' => count($edges),
                ],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
