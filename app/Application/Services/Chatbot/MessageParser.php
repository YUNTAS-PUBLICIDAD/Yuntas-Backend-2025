<?php

namespace App\Application\Services\Chatbot;

class MessageParser
{
  public function extractName(string $message): ?string
  {
    $message = strtolower($message);
     if (preg_match('/(me llamo|soy|mi nombre es)\s+([a-zA-Z]+)/', $message, $matches)) {
            return ucfirst($matches[2]);
        }

        return null;
  }
}