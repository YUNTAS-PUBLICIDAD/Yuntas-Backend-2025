<?php
namespace App\Application\Services\Chatbot;

use App\Models\ChatbotConversation;

// Encargado de orquestar todo
/**
 * FLUJO
 * 1. Usuario envía mensaje
 * 2. Guardar mensaje
 * 3. Detectar intención
 * 4. Buscar question -> answer
 * 5. Enviar respuesta
 * 6. Recoger acciones:
 *  - del intent
 *  - del answer
 * 7. Ordenar por priority
 * 8. Evaluar condiciones
 * 9. Ejecutar acciones
 * 10. Actualizar contexto
 */
class ChatBotEngine
{
  public function handleMessage(ChatbotConversation $conversation, string $message)
  {
    // Guardar mensaje
    $conversation->messages()->create([
      'message_text' => $message,
      'sender' => 'user',
      'timestamp' => now()
    ]);

    // Detectar intent
    $intent = app(IntentMatcher::class)->match($message);

    if (!$intent) {
      return $this->fallback($conversation);
    }
    // Obtener respuesta
    $question = $intent->questions()->inRandomOrder()->first();
    $answer = $question->answers()->inRandomOrder()->first();

    if (!$answer) {
      return $this->fallback($conversation);
    }

    // Responder
    $this->sendMessage($conversation, $answer->answer_text);

    // Acciones
    $actions = $this->collectActions($intent, $answer);

    // Ejecutar acciones
    app(ActionExecutor::class)->execute($actions, $conversation,$message );
  }

  protected function collectActions($intent, $answer)
  {
    return collect()
    ->merge($intent->actions)
    ->merge($answer->actions)
    ->sortBy('pivot.priority');
  }

  protected function sendMessage($conversation, $text)
  {
    $conversation->messages()->create([
      'message_text' => $text,
      'sender' => 'bot',
      'timestamp' => now()
    ]);
  }

  protected function fallback($conversation)
  {
    $this->sendMessage($conversation, 'No entendí, ¿puedes reformular');
  }
}