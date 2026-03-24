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

    // // Responder
    // $this->sendMessage($conversation, $answer->answer_text);

    // Acciones primero (actualizar contexto)
    $actions = $this->collectActions($intent, $answer);
    $validActions = app(ActionExecutor::class)->filterExecutable($actions, $conversation, $message);

    // app(ActionExecutor::class)->execute($actions, $conversation,$message );
    app(ActionExecutor::class)->execute($validActions, $conversation, $message);

    // Parsea con el contexto ya actualizado
    $parsedText = $this->parseMessage($answer->answer_text, $conversation);

    // Y enviar UNA sola respuesta
    $this->sendMessage($conversation, $parsedText);
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

  protected function parseMessage($text, $conversation)
  {
    $context = $conversation->context ?? [];

    //  return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($context) {
    //     return data_get($context, trim($matches[1]), '');
    // }, $text);
     return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($context) {
        $expression = trim($matches[1]);

        // soporte básico para default: user_name|👋
        if (str_contains($expression, '|')) {
            [$key, $default] = explode('|', $expression);
            return data_get($context, trim($key), trim($default));
        }

        return data_get($context, $expression, '');
    }, $text);
  }
}