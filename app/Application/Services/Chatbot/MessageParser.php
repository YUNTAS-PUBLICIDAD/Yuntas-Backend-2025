<?php

namespace App\Application\Services\Chatbot;

class MessageParser
{
  public function extractName(string $message): ?string
  {
    $message = trim($message);

    if (preg_match('/\b(me llamo|soy|mi nombre es)\b\s+([a-zA-ZáéíóúÁÉÍÓÚñÑ]{2,})/i', $message, $matches)) {
        return $this->formatName($matches[2]);
    }

    return null;
  }
  private function formatName(string $name): string { return ucwords(strtolower($name)); }
}