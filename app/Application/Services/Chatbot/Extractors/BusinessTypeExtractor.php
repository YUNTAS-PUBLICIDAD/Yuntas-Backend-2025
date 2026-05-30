<?php

namespace App\Application\Services\Chatbot\Extractors;

class BusinessTypeExtractor
{
  protected array $map = [

  'restaurante' => [
        'restaurante',
        'restaurant',
        'comida'
    ],

    'polleria' => [
        'polleria',
        'pollo a la brasa'
    ],

    'cevicheria' => [
        'cevicheria',
        'ceviche'
    ],

    'cafeteria' => [
        'cafeteria',
        'cafe',
        'coffee shop',
        'cafetería'
    ],

    'bar' => [
        'bar',
        'pub',
        'cantina'
    ],

    'discoteca' => [
        'discoteca',
        'club nocturno',
        'night club'
    ],

    'barberia' => [
        'barberia',
        'barber',
        'barbershop'
    ],

    'salon_belleza' => [
        'salon',
        'salon de belleza',
        'peluqueria',
        'estetica'
    ],

    'gimnasio' => [
        'gym',
        'gimnasio',
        'crossfit',
        'fitness'
    ],

    'clinica_estetica' => [
        'clinica estetica',
        'medicina estetica',
        'spa',
        'spa facial'
    ],

    'odontologia' => [
        'odontologia',
        'dentista',
        'consultorio dental'
    ],

    'hotel' => [
        'hotel',
        'hostal',
        'hospedaje'
    ],

    'retail' => [
        'retail',
        'tienda',
        'boutique',
        'showroom'
    ],

    'ropa' => [
        'tienda de ropa',
        'ropa',
        'moda',
        'boutique'
    ],

    'tecnologia' => [
        'tecnologia',
        'computadoras',
        'celulares',
        'electronica'
    ],

    'farmacia' => [
        'farmacia',
        'botica'
    ],

    'inmobiliaria' => [
        'inmobiliaria',
        'proyecto inmobiliario'
    ],

    'casino' => [
        'casino',
        'tragamonedas'
    ],

    'centro_comercial' => [
        'centro comercial',
        'mall'
    ],

    'feria' => [
        'feria',
        'stand',
        'modulo comercial'
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
