<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $products = [
      ['name' => 'Letrero LED Publicitario', 'price' => 350],
      [
        'name' => 'Letrero Neón Personalizado',
        'price' => 420
      ],
      [
        'name' => 'Letras Corpóreas Acrílicas',
        'price' => 500
      ],
      [
        'name' => 'Caja de Luz Publicitaria',
        'price' => 300
      ],
      [
        'name' => 'Pantalla LED Exterior',
        'price' => 1200
      ],
      [
        'name' => 'Proyector Holográfico 3D',
        'price' => 1500
      ],
      [
        'name' => 'Señalética Comercial',
        'price' => 250
      ]
    ];

    foreach ($products as $data) {
      Product::firstOrCreate(
        ['slug' => Str::slug($data['name'])],
        [
          'name' => $data['name'],
          'slug' => Str::slug($data['name']),
          'hero_title' => $data['name'],
          'description' => "Solución profesional de {$data['name']}",
          'price' => $data['price'],
          'status' => 'active',
          'meta_title' => $data['name'],
          'meta_description' => $data['name'],
          'keywords' => [$data['name']]
        ]
      );
    }
  }
}
