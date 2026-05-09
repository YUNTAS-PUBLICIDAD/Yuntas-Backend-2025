<?php

namespace Database\Seeders;

use App\Models\Category;
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
    // ─────────────────────────────
           // CATEGORÍAS
           // ─────────────────────────────

           $categories = [

               'Proyectores',

               'Iluminación LED',

               'Señalización y Letreros',

               'Pantallas y Paneles LED',

               'Mobiliario LED',

               'Publicidad Digital',
           ];

           $categoryMap = [];

           foreach ($categories as $name) {

               $category = Category::firstOrCreate(
                   [
                       'slug' => Str::slug($name)
                   ],
                   [
                       'name' => $name,
                       'slug' => Str::slug($name)
                   ]
               );

               $categoryMap[$name] = $category;
           }

           // ─────────────────────────────
           // PRODUCTOS
           // ─────────────────────────────

           $products = [

               // PROYECTORES
               [
                   'name' => 'Proyector Holográfico 3D',
                   'price' => 1500,
                   'categories' => [
                       'Proyectores'
                   ]
               ],

               // ILUMINACIÓN LED
               [
                   'name' => 'Piso LED Interactivo',
                   'price' => 1800,
                   'categories' => [
                       'Iluminación LED'
                   ]
               ],

               [
                   'name' => 'Techo LED Decorativo',
                   'price' => 2200,
                   'categories' => [
                       'Iluminación LED'
                   ]
               ],

               [
                   'name' => 'Barra Pixel LED',
                   'price' => 650,
                   'categories' => [
                       'Iluminación LED'
                   ]
               ],

               [
                   'name' => 'Neón LED Flexible',
                   'price' => 480,
                   'categories' => [
                       'Iluminación LED'
                   ]
               ],

               [
                   'name' => 'Letrero Neón LED',
                   'price' => 520,
                   'categories' => [
                       'Iluminación LED',
                       'Señalización y Letreros'
                   ]
               ],

               // SEÑALIZACIÓN
               [
                   'name' => 'Letrero Acrílico Empresarial',
                   'price' => 750,
                   'categories' => [
                       'Señalización y Letreros'
                   ]
               ],

               [
                   'name' => 'Letras MDF Pintadas',
                   'price' => 420,
                   'categories' => [
                       'Señalización y Letreros'
                   ]
               ],

               [
                   'name' => 'Menu Board LED',
                   'price' => 980,
                   'categories' => [
                       'Señalización y Letreros'
                   ]
               ],

               [
                   'name' => 'Letras Doradas Premium',
                   'price' => 890,
                   'categories' => [
                       'Señalización y Letreros'
                   ]
               ],

               [
                   'name' => 'Impresión en Vinilo HD',
                   'price' => 300,
                   'categories' => [
                       'Señalización y Letreros'
                   ]
               ],

               [
                   'name' => 'Letrero Luminoso Exterior',
                   'price' => 1200,
                   'categories' => [
                       'Señalización y Letreros'
                   ]
               ],

               // PANTALLAS
               [
                   'name' => 'Pantalla LED Electrónica',
                   'price' => 3500,
                   'categories' => [
                       'Pantallas y Paneles LED'
                   ]
               ],

               [
                   'name' => 'Panel LED Publicitario',
                   'price' => 2800,
                   'categories' => [
                       'Pantallas y Paneles LED'
                   ]
               ],

               // MOBILIARIO
               [
                   'name' => 'Mesa LED Lounge',
                   'price' => 1300,
                   'categories' => [
                       'Mobiliario LED'
                   ]
               ],

               [
                   'name' => 'Silla LED RGB',
                   'price' => 780,
                   'categories' => [
                       'Mobiliario LED'
                   ]
               ],

               // PUBLICIDAD DIGITAL
               [
                   'name' => 'Monitor Publicitario Digital',
                   'price' => 2400,
                   'categories' => [
                       'Publicidad Digital'
                   ]
               ],
           ];

           foreach ($products as $data) {

               $product = Product::firstOrCreate(
                   [
                       'slug' => Str::slug($data['name'])
                   ],
                   [
                       'name' => $data['name'],

                       'slug' => Str::slug($data['name']),

                       'hero_title' => $data['name'],

                       'description' =>
                           "Solución profesional de {$data['name']}",

                       'price' => $data['price'],

                       'status' => 'active',

                       'meta_title' => $data['name'],

                       'meta_description' => $data['name'],

                       'keywords' => [$data['name']]
                   ]
               );

               $categoryIds = collect($data['categories'])
                   ->map(fn ($name) => $categoryMap[$name]->id)
                   ->values()
                   ->toArray();

               $product->categories()
                   ->syncWithoutDetaching($categoryIds);
           }

  }
}
