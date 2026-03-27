<?php

namespace App\Application\Support;

use App\Models\Lead;

class TemplateVariableBuilder
{
  public static function forLead(Lead $lead): array
  {
    $lead->loadMissing('product');
    // $base = [
    //   'nombre' => $lead->name,
    //   'email' => $lead->email
    // ];

    // if ($lead->product_id && $lead->product) {
    //   return array_merge($base, [
    //     'producto_nombre' => $lead->product->name,
    //     'descripcion' => $lead->product->description,
    //     'fecha' => now('America/Lima')->format('d/m/Y'),
    //     'hora' => now('America/Lima')->format('H:i'),
    //   ]);
    // }

    // return $base;
    return [
      'nombre' => $lead->name,
       'email' => $lead->email,
        'telefono' => $lead->phone,
        'fecha' => now('America/Lima')->format('d/m/Y'),
        'hora' => now('America/Lima')->format('H:i'),

        // producto (aunque sea null-safe)
        'producto_nombre' => $lead->product->name ?? '',
        'descripcion' => $lead->product->description ?? '',
    ];
  }
}