<?php

namespace App\Application\Services\Chatbot;

use App\Models\ChatbotIntent;

class IntentMatcher
{
  public function match(string $message)
  {
    $message = strtolower($message);

    return ChatbotIntent::with('questions')
    ->get()
    ->first(function ($intent) use ($message) {
      return $intent->questions->contains(function ($q) use ($message){
        $keywords = $q->keywords ?? [];

        foreach ($keywords as $keyword) {
          if (str_contains($message, strtolower($keyword))) {
            return true;
          }
        }
        return false;
      });
    });
  }
}