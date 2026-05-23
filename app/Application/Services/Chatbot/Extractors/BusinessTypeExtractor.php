<?php

namespace App\Application\Services\Chatbot\Extractors;

class BusinessTypeExtractor
{
  protected array $map = [

          'restaurante' => [
              'restaurante',
              'polleria',
              'cevicheria',
              'fast food',
              'comida'
          ],

          'retail' => [
              'tienda',
              'retail',
              'minimarket',
              'market'
          ],

          'barberia' => [
              'barberia',
              'barber',
              'salon'
          ]
      ];

      public function extract(string $message): ?string
      {
          $message = strtolower($message);

          foreach ($this->map as $value => $keywords) {

              foreach ($keywords as $keyword) {

                  if (str_contains($message, $keyword)) {
                      return $value;
                  }
              }
          }

          return null;
      }
}
