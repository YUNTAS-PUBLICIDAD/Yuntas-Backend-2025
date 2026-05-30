<?php
 
namespace Database\Seeders;
 
use App\Models\TrackingPage;
use Illuminate\Database\Seeder;
 
class TrackingPagesTableSeeder extends Seeder
{
    /**
     * Correr el seeder.
     */
    public function run(): void
    {
        $pages = [
            ['route' => '/', 'name' => 'Inicio'],
            ['route' => '/nosotros', 'name' => 'Nosotros'],
            ['route' => '/productos', 'name' => 'Productos'],
            ['route' => '/blog', 'name' => 'Blog'],
            ['route' => '/contacto', 'name' => 'Contacto'],
        ];
 
        foreach ($pages as $page) {
            TrackingPage::updateOrCreate(
                ['route' => $page['route']],
                ['name' => $page['name']]
            );
        }
    }
}
