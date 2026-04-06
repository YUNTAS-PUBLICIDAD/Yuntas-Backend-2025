<?php

namespace App\Http\Controllers\Chatbot;

use App\Application\Services\Chatbot\Engine\ChatbotEngine;
use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
      $request->validate([
        'message' => 'required|string',
        // 'conversation_id' => 'nullable|exists:chatbot_conversations,id',
        'conversation_id' => 'nullable|exists:chatbot_conversations,uuid',
        'lead_id' => 'nullable|exists:leads,id'
      ]);

      // Obtener conversación
      $conversation = $this->resolveConversation($request);

      // Log para rastrear conversaciones
Log::info('Con conversación resuelta', [
    'conversation_id' => $conversation->id,
    'lead_id' => $conversation->lead_id,
    'context' => $conversation->context
]);

      // Ejecutar motor
      app(ChatbotEngine::class)->handleMessage($conversation, $request->message);

      // Devolver últimos mensajes
      return response()->json([
        // 'conversation_id' => $conversation->id,
        'conversation_id' => $conversation->uuid,
        'messages' => $conversation->messages()->latest()->take(8)->get()->reverse()->values()]);
    }

    protected function resolveConversation($request)
    {
      if ($request->conversation_id) {
        // return ChatbotConversation::findOrFail( $request->conversation_id);
        return ChatbotConversation::where('uuid', $request->conversation_id)->firstOrFail();
      }
      return ChatbotConversation::create([
        'lead_id' => $request->lead_id,
        'started_at'=> now(),
        'context' => []
      ]);
    }
}
