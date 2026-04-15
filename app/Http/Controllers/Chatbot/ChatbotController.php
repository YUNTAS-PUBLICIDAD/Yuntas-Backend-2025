<?php

namespace App\Http\Controllers\Chatbot;

use App\Application\Services\Chatbot\Engine\ChatbotEngine;
use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\Lead;
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

    public function whatsapp(Request $request)
    {
      $phone = $request->phone;
      $message = $request->message;

      // $cleanPhone = preg_replace('/\D/', '', $phone);

      // // Asegurar formato Perú
      // if (strlen($cleanPhone) === 9) {
      //     $cleanPhone = '51' . $cleanPhone;
      // }

      $cleanPhone = $this->normalizePhone($phone);

      if (!preg_match('/^9\d{8}$/', $cleanPhone)) {
        return response()->json([
        'error' => 'Número inválido'
        ], 400);
      }

      // Log::info('WHATSAPP INPUT', [
      // 'raw_phone' => $phone,
      // 'clean_phone' => $cleanPhone,
      // 'message' => $message
      // ]);

      // $existingLead = Lead::where('phone', $cleanPhone)->first();
      // Log::info('LEAD SEARCH RESULT', [
      //   'found' => !!$existingLead,
      //   'lead_id' => $existingLead?->id,
      //   'lead' => $existingLead?->phone
      // ]);

      // Buscar por número en lead sino crearlo
      // 1. Lead siempre único por teléfono
      $lead = Lead::firstOrCreate(
        ['phone' => $cleanPhone],
        [
          'name' => 'Usuario Whatsapp',
          // 'email' => null,
          'email' => 'temp_' . $cleanPhone . '@noemail.com',
          'source_id' => null
        ]
      );

      // Log::info('LEAD FINAL', [
      //   'lead_id' => $lead->id,
      //   'phone' => $lead->phone
      // ]);

      // 2. Conversación siempre ligada al lead
      $conversation = ChatbotConversation::firstOrCreate(
        [
          'channel' => 'whatsapp',
          'external_id' => $cleanPhone
        ],
        [
          'lead_id' => $lead->id,
          'started_at' => now(),
          'context' => []
        ]
      );

      // 3. Garantizar consistencia (sin if raro)
      if($conversation->lead_id !== $lead->id){
        $conversation->update(['lead_id' => $lead->id]);
      }
      // 4. Chatbot
      $response = app(ChatbotEngine::class)
        ->handleMessage($conversation, $message, 'whatsapp');

      return response()->json([
        'reply' => $response
      ]);
    }

    function normalizePhone($phone)
    {
      $clean = preg_replace('/\D/', '', $phone);
      // Si viene con 51 -> lo quitamos
      if (strlen($clean) === 11 && str_starts_with($clean, '51')) {
        return substr($clean, 2);
      }
      return $clean;
    }
}
