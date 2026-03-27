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

  public function extractPhone(string $message): ?string
{

// Normalizar
$clean = strtolower($message);

// Eliminar palabras comunes
 $clean = preg_replace('/(mi numero es|mi número es|numero|número|telefono|tel|celular|whatsapp|ws|:)/i', '', $clean);

 // Quitar espacios inncesarios
 $clean = trim($clean);

 // Buscar número Perú (9 dígitos o +51...)
 if (preg_match('/(\+?51)?\s*9\d{8}/', $clean, $matches)) {
        // Limpiar resultado final
        return preg_replace('/\D/', '', $matches[0]);
    }
  return null;

  }
}
