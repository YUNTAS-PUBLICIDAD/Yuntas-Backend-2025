<?php

namespace App\Application\Services\Chatbot\Intent;

use App\Models\ChatbotIntent;

class IntentMatcher
{
  public function match(string $message)
  {
    $message = strtolower($message);

    // Limpiar texto básico
    $message = preg_replace('/[^\w\s]/', '', $message);
    $words = explode(' ', $message);

    $stopWords = ['quiero', 'necesito', 'deseo', 'porfavor'];

    $intents = ChatbotIntent::with('questions')->get();

    $bestIntent = null;
    $bestScore = 0;

    foreach ($intents as $intent) {
      $score = 0;

      foreach ($intent->questions as $q) {
        $keywords = $q->keywords ?? [];

        // $words = explode(' ', $message);

        foreach ($keywords as $keyword) {
          // if (str_contains($message, strtolower($keyword))) {
          //   $score++;
          // }
          $keyword = strtolower($keyword);

          // Ignorar basura
          if(in_array($keyword, $stopWords)){
            continue;
          }

          // match exacto palabra
          if (in_array($keyword, $words)) {
            $score += 3;
            continue;
          }

          // match frase completa
          if (str_contains($message, $keyword)) {
            // Palabras más largas = más específicas
            $score += strlen($keyword) > 5 ? 2 : 1;
          }
        }
      }
      if ($score > $bestScore) {
        $bestScore = $score;
        $bestIntent = $intent;
      }
    }
    return $bestScore >= 1 ? $bestIntent : null;

    // return ChatbotIntent::with('questions')
    // ->get()
    // ->first(function ($intent) use ($message) {
    //   return $intent->questions->contains(function ($q) use ($message){
    //     $keywords = $q->keywords ?? [];

    //     foreach ($keywords as $keyword) {
    //       if (str_contains($message, strtolower($keyword))) {
    //         return true;
    //       }
    //     }
    //     return false;
    //   });
    // });
  }
}
