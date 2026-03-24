<?php

namespace App\Application\Services\Chatbot;

class MessageParser
{
  public function extractName(string $message): ?string
  {
    // $message = strtolower($message);
    //  if (preg_match('/(me llamo|soy|mi nombre es)\s+([a-zA-Z]+)/', $message, $matches)) {
    //         return ucfirst($matches[2]);
    //     }

    //     return null;
    // Mantener original para formato final
    $original = $message;

    // Normalizar para detectar patrón
    $normalized = strtolower($message);
    
     if (preg_match('/(me llamo|soy|mi nombre es)\s+(.+)/i', $normalized, $matches)) {

            $namePart = $matches[2];

            // Limpiar basura: emojis, símbolos raros
            $namePart = preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/u', '', $namePart);

            // Tomar máximo 2 palabras (nombre + apellido)
            $words = array_slice(explode(' ', trim($namePart)), 0, 2);

            $name = implode(' ', $words);

            return ucwords($name);
        }
        return null;
  }
}