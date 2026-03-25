<?php
namespace App\Application\Services\Chatbot;

use App\Application\Services\Chatbot\States\ConversationState;
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
    $conversation->refresh();

    
    // Messages -> SOLO historial visible (chat)
    // Context -> SOLO estado útil del bot
    // Memoria útil vs ruido
    // Guardar en context: nombre, email, intención actual, paso de flujo o decisiones del usuario
    // No guardar mensajes completos, textos largos o historial redundante
    // Obtener contexto actual
    $context = $conversation->context ?? [];

    // Log para verificar en preguntar nombre
    \Log::info('Comprobando user.name antes de preguntar', [
    'conversation_id' => $conversation->id,
    'user_name' => data_get($context, 'user.name')]);

    // Permitir que el intent "nombre" pase aunque no haya nombre
    // $hasName = !empty(trim(data_get($context, 'user.name', '')));
//     $hasName = filled(data_get($context, 'user.name'));

//     // SI NO HAY NOMBRE -> lo capturamos directo
//     if (!$hasName) {
//        $name = app(MessageParser::class)->extractName($message);

//        if ($name) {
//         data_set($context, 'user.name', $name);

//         // Limpiar mensaje
//          $message = preg_replace('/(me llamo|soy|mi nombre es)\s+' . preg_quote($name, '/') . '/i', '', $message);
//    $message = trim($message);

//         $conversation->update([
//           'context' => $context
//         ]);
//         // Log justo después de actualizar
// \Log::info('Contexto después de capturar nombre', [
//     'conversation_id' => $conversation->id,
//     'context' => $conversation->context
// ]);

//         $conversation->refresh();
//         $context = $conversation->context ?? [];

//         // // Responder inmediatamente
//         return $this->sendMessage($conversation, "Perfecto, {$name} 👍");
//        }else {

//          // Si no puedo extraer -> seguir preguntando
//          return $this->askName($conversation);
//        }
//     }

// Obtener estado actual STATE MACHINE (FSM)
$state = data_get($context, 'conversation.state');
$lockedStates = [
  ConversationState::ASKING_PROJECT_TYPE
];

if (in_array($state, $lockedStates)) {
  return $this->handleProjectType($conversation, $message);
}

// Estado inicial si no existe
if (!$state) {
  $state = ConversationState::ASKING_NAME;
  data_set($context, 'conversation.state', $state);
  $conversation->update(['context' => $context]);
}

// ==============
// ESM MANDA PRIMERO
// ==============

switch ($state) {
  case ConversationState::ASKING_NAME:
    return $this->handleAskingName($conversation, $message);
  case ConversationState::ASKING_PROJECT_TYPE:
    return $this->handleProjectType($conversation, $message);
  case ConversationState::CLOSING_LEAD:
    return $this->handleClosing($conversation);
  case ConversationState::READY: 
  default:
    return $this->handleIntentFlow($conversation, $message);
}

    // // Detectar intent
    // $intent = app(IntentMatcher::class)->match($message);
    // if (!$intent) {
    //   return $this->fallback($conversation);
    //   }
      

    // // Guardar intent de contexto
    // data_set($context, 'conversation.intent', $intent->name); 
    // $conversation->update([
    //   'context' => $context
    //   ]);
    //   $conversation->refresh();
    //   $context = $conversation->context ?? [];
    //   // Obtener respuesta base
    //   $question = $intent->questions()->inRandomOrder()->first();
    //   $answer = $question->answers()->inRandomOrder()->first();
      
    //   if (!$answer) {
    //     return $this->fallback($conversation);
    //     }
        
    //     // // Responder
    //     // $this->sendMessage($conversation, $answer->answer_text);
        
    //     // Acciones primero (actualizar contexto)
    //     $actions = $this->collectActions($intent, $answer);
    //     $validActions = app(ActionExecutor::class)->filterExecutable($actions, $conversation, $message);
        
    //     // Ejecutar acciones
    //     // app(ActionExecutor::class)->execute($actions, $conversation,$message );
    //     app(ActionExecutor::class)->execute($validActions, $conversation, $message);
    //     // Refrescar contexto actualizado
    //     $conversation->refresh();
    //     $context = $conversation->context ?? [];
     

    // // Parsear mensaje con contexto actualizado
    // $parsedText = $this->parseMessage($answer->answer_text, $conversation);

    // // Responder
    // $this->sendMessage($conversation, $parsedText);
  }

  // protected function askName($conversation)
  // {
  //   $this->sendMessage($conversation, 'Antes de continuar, ¿cómo te llamas?');
  // }

  // ==================
  // STATE: ASKING NAME
  // =================
  protected function handleAskingName($conversation, $message)
  {
    $context = $conversation->context ?? [];

    $name = app(MessageParser::class)->extractName($message);

    if (!$name) {
      return $this->sendMessage($conversation, 'Antes de continuar ¿Cómo te llamas?');
    }

    // Guardar nombre
    data_set($context, 'user.name', $name);

    // Cambiar estado
    data_set($context, 'conversation.state', ConversationState::READY);

    $conversation->update(['context' => $context]);

    $message = preg_replace('/(me llamo|soy|mi nombre es)\s+' . preg_quote($name, '/') . '/i', '', $message);
$message = trim($message);
$this->sendMessage($conversation, "Perfecto, {$name} 👍");
if ($message) {
  return $this->handleIntentFlow($conversation, $message);
}

    return;
  }

  // ===================
  // STATE: PROJECT TYPE
  protected function handleProjectType($conversation, $message)
  {
    $context = $conversation->context ?? [];
    if (strlen(trim($message)) < 3) {
      return $this->sendMessage($conversation, '¿Puedes darme más detalle del proyecto?');
    }
    data_set($context, 'lead.project_type', $message);
    data_set($context, 'conversation.state', ConversationState::CLOSING_LEAD);
    $conversation->update(['context' => $context]);
    return $this->sendMessage($conversation, 'Genial, te contacto en breve 👍');
  }

  // ================
  // STATE: CLOSING
  // ================
  protected function handleClosing($conversation)
  {
    $context = $conversation->context ?? [];
    // RESET a estado base
    data_set($context, 'conversation.state', ConversationState::READY);
    $conversation->update(['context' => $context]);
    return $this->sendMessage($conversation, 'Gracias por tu interés 🙌');
  }

  // =================
  // INTENT FLOW
  // =================
  protected function handleIntentFlow($conversation, $message)
  {
    $context = $conversation->context ?? [];

    $intent = app(IntentMatcher::class)->match($message);

    if (!$intent) {
      return $this->fallback($conversation);
    }

    // Guardar intent
    data_set($context, 'conversation.intent', $intent->name);
    $conversation->update(['context' => $context]);

    // Obtener respuesta
    $question = $intent->questions()->inRandomOrder()->first();
    $answer = $question?->answers()->inRandomOrder()->first();

    if (!$answer) {
      return $this->fallback($conversation);
    }

    $prevState = data_get($conversation->context, 'conversation.state');

    // Ejecutar acciones
    $actions = $this->collectActions($intent, $answer);
    $validActions = app(ActionExecutor::class)
    ->filterExecutable($actions, $conversation, $message);

    app(ActionExecutor::class)
    ->execute($validActions, $conversation, $message);

    $conversation->refresh();

    $newState = data_get($conversation->context, 'conversation.state');

    \Log::info('STATE TRANSITION', [
      'from' => $prevState,
      'to' => $newState
    ]);

    // Parsear variables
    $parsed = $this->parseMessage($answer->answer_text, $conversation);

    return $this->sendMessage($conversation, $parsed);
  }

  
  // ================
  // UTILIDADES
  // ================
  protected function sendMessage($conversation, $text)
  {
    $conversation->messages()->create([
      'message_text' => $text,
      'sender' => 'bot',
      'timestamp' => now()
      ]);
      $this->trimMessages($conversation);
      }
      
      protected function collectActions($intent, $answer)
      {
        return collect()
        ->merge($intent->actions)
        ->merge($answer->actions)
        ->sortBy('pivot.priority');
      }

  protected function fallback($conversation)
  {
    return $this->sendMessage($conversation, 'No entendí, ¿puedes reformular?');
  }

  protected function parseMessage($text, $conversation)
  {
    $context = $conversation->context ?? [];

    //  return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($context) {
    //     return data_get($context, trim($matches[1]), '');
    // }, $text);
     return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($context) {
        // $expression = trim($matches[1]);

        // // soporte básico para default: user_name|👋
        // if (str_contains($expression, '|')) {
        //     // [$key, $default] = explode('|', $expression);
        //     [$key, $default] = array_pad(explode('|', $expression,2),2, '');
        //     return data_get($context, trim($key), trim($default));
        // }

        // return data_get($context, $expression, '');

        // sperar por pipes: key | default | transform
        // $parts = array_map('trim', explode('|', $expression));
        $parts = array_map('trim', explode('|', $matches[1]));

        $key = array_shift($parts);
        // Obtener valor del contexto
        $value = data_get($context, $key);

        // Si no existe valor -> usar default (si hay)
        if (!$value && count($parts)) {
          $value = array_shift($parts);
        }

        // Aplicar transformaciones
        foreach ($parts as $modifier) {
          // $value = $this->applyModifier($value, $modifier);
          $value = match ($modifier) {
            'upper' => strtoupper($value),
            'lower' => strtolower($value),
            'title' => ucwords($value),
            default => $value
          };
        }

        return $value ?? '';
    }, $text);
  }

  protected function applyModifier($value, $modifier)
  {
    return match ($modifier) {
      'upper' => strtoupper($value),
      'lower' => strtolower($value),
      'ucfirst' => ucfirst($value),
      'title' => ucwords($value),
      default => $value
    };
  }

  protected function trimMessages($conversation, $limit = 50)
  {
    // $conversation->messages()
    // ->latest()->skip($limit)
    // ->take(PHP_INT_MAX)
    // ->delete();
    $idsToKeep = $conversation->messages()
    ->latest('timestamp')
    ->take($limit)
    ->pluck('id');

    $conversation->messages()
    ->whereNotIn('id', $idsToKeep)
    ->delete();
  }
}