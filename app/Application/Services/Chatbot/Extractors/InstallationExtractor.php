<?php

namespace  App\Application\Services\Chatbot\Extractors;

class InstallationExtractor
{
  protected array $map  = [
    'interior' => [
      'interior',
      'adentro',
      'dentro'
    ],
    'exterior' => [
      'exterior',
      'afuera',
      'fachada',
      'calle'
    ]
  ];

  public function extract(string $message): ?string
  {
    $message = strtolower($message);

    foreach ($this->map as $value => $keywords) {
      foreach ($keywords as $keyword) {
        if(str_contains($message, $keyword)){
          return $value;
        }
      }
    }
    return null;
  }
}
