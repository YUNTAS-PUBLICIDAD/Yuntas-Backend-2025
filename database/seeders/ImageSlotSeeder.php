<?php

namespace Database\Seeders;

use App\Models\ImageSlot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slots = [
            // Blogs
            ['module' => 'blogs', 'name' => 'List'],
            ['module' => 'blogs', 'name' => 'Hero'],
            ['module' => 'blogs', 'name' => 'Desc'],
            ['module' => 'blogs', 'name' => 'Benefits'],
            ['module' => 'blogs', 'name' => 'Testimonial'],

            // PRODUCTS
            ['module' => 'products', 'name' => 'List'],
            ['module' => 'products', 'name' => 'Hero'],
            ['module' => 'products', 'name' => 'Specs'],
            ['module' => 'products', 'name' => 'Benefits'],

            // POPUPS (los importantes)
            ['module' => 'products', 'name' => 'PopupLeft'],
            ['module' => 'products', 'name' => 'PopupRight'],
            ['module' => 'products', 'name' => 'PopupMobile']
        ];

        foreach($slots as $slot){
          ImageSlot::updateOrCreate($slot);
        }
    }
}
