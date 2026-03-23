<?php

namespace App\Http\Controllers\Chatbot;

use App\Application\Services\Chatbot\ChatBotEngine;
use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
      $request->validate([
        'message' => 'required|string',
        'conversation_id' => 'nullable|exists:chatbot_conversations,id',
        'lead_id' => 'nullable|exists:leads,id'
      ]);

      // Obtener conversación
      $conversation = $this->resolveConversation($request);

      // Ejecutar motor
      app(ChatBotEngine::class)->handleMessage($conversation, $request->message);

      // Devolver últimos mensajes
      return response()->json([
        'conversation_id' => $conversation->id, 'messages' => $conversation->messages()->latest()->take(8)->get()->reverse()->values()]);
    }

    protected function resolveConversation($request)
    {
      if ($request->conversation_id) {
        return ChatbotConversation::findOrFail( $request->conversation_id);
      }
      return ChatbotConversation::create([
        'lead_id' => $request->lead_id,
        'started_at'=> now(),
        'context' => []
      ]);
    }
}
