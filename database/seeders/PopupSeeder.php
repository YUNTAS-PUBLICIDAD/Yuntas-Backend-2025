<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Popup;

class PopupSeeder extends Seeder
{
    public function run(): void
    {
        $popups = [
            [
                'lead_source_id' => 1,
                'slug' => 'accede-a-un-descuento-en-tu-primera-compra',
                'title' => 'Accede a un descuento en tu primera compra',
                'button_text' => 'Enviar',
                'button_text_color' => '#ffffff',
                'button_color' => '#802aea',
                'page_target' => 'inicio',
                'delay_seconds' => 5,
                'priority' => 1,
                'active' => true,
            ],

            [
                'lead_source_id' => 3,
                'slug' => 'promocion-especial',
                'title' => 'Promoción especial',
                'button_text' => 'COTIZAR AHORA',
                'button_text_color' => '#ffffff',
                'button_color' => '#802aea',
                'page_target' => 'product-detail',
                'delay_seconds' => 12,
                'priority' => 1,
                'active' => true,
            ],
            
        ];

        foreach ($popups as $data) {
            Popup::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}