<?php

namespace App\Application\Support;

use App\Models\Lead;

class TemplateVariableBuilder
{
  public static function forLead(Lead $lead): array
  {

    // // return $base;
    // return [
    //   'nombre' => $lead->name,
    //    'email' => $lead->email,
    //     'telefono' => $lead->phone,
    //     'fecha' => now('America/Lima')->format('d/m/Y'),
    //     'hora' => now('America/Lima')->format('H:i'),

    //     // producto (aunque sea null-safe)
    //     'producto_nombre' => $lead->product->name ?? '',
    //     'descripcion' => $lead->product->description ?? '',
    // ];

      return array_merge(
        self::base($lead),
        self::product($lead),
        self::time()
      );
  }

  private static function base(Lead $lead): array
  {
    return [
      'nombre' => $lead->name,
      'email' => $lead->email,
      'telefono' => $lead->phone
    ];
  }

  private static function product(Lead $lead): array
  {
    return [
      'producto_nombre' => $lead->product->name ?? '',
      'descripcion' => $lead->product->description ?? ''
    ];
  }

  private static function time(): array
  {
  return [
    'fecha' => now('America/Lima')->format('d/m/Y'),
    'hora' => now('America/Lima')->format('H:i'),
  ];
  }

  public static function schema(): array
  {
    return [
      'variables' => [
        'nombre',
        'email',
        'telefono',
        'producto_nombre',
        'descripcion',
        'fecha',
        'hora'
      ],
      'preview' => [
      'nombre' => 'Juan Pérez',
      'email' => 'juan@email.com',
      'telefono' => '999999999',
      'producto_nombre' => 'Proyecto holografico',
      'descripcion' => 'Los proyectores holográficos 3D, también conocidos como ventiladores holográficos, proyectan imágenes tridimensionales',
      'fecha' => now('America/Lima')->format('d/m/Y'),
      'hora' => now('America/Lima')->format('H:i'),
      ]
    ];
  }
}
