<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use App\Models\LeadSource;
use App\Models\ImageSlot;
use App\Models\ProductContentSlot;
use App\Models\BlogContentSlot;
use App\Models\DocumentType;
use App\Models\ClaimStatus;
use App\Models\ClaimType;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // 2. Usuario Administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id, // Asignación manual
        ]);

        // 3. Slots de Imágenes
        ImageSlot::firstOrCreate(['module' => 'blogs', 'name' => 'Main'], ['position' => 1]);
        ImageSlot::firstOrCreate(['module' => 'blogs', 'name' => 'Gallery'], ['position' => 2]);

        // 4. Slots de Contenido
        ProductContentSlot::firstOrCreate(['name' => 'Especificaciones'], ['data_type' => 'list', 'position' => 1]);
        ProductContentSlot::firstOrCreate(['name' => 'Beneficios'], ['data_type' => 'list', 'position' => 2]);

        BlogContentSlot::firstOrCreate(['name' => 'Parrafos'], ['data_type' => 'text', 'position' => 1]);
        BlogContentSlot::firstOrCreate(['name' => 'Beneficios'], ['data_type' => 'list', 'position' => 2]);
        BlogContentSlot::firstOrCreate(['name' => 'Bloques'], ['data_type' => 'block', 'position' => 3]);

        // 5. Fuentes de Leads
        LeadSource::firstOrCreate(['name' => 'Web']);
        LeadSource::firstOrCreate(['name' => 'Facebook']);

        // 6. Categorías
        Category::firstOrCreate(['name' => 'Laptops', 'slug' => 'laptops', 'description' => 'Portátiles']);

        // 7. Producto Demo
        Product::create([
            'name' => 'Laptop Demo',
            'slug' => 'laptop-demo',
            'hero_title' => 'Laptop Demo',
            'description' => 'Descripción...',
            'price' => 1000.00,
            'status' => 'active',
            'meta_title' => 'SEO Title',
            'keywords' => ['demo'],
        ]);

        // 7. Documento ID
        DocumentType::create([
            'code' => '1',
            'label' => 'dni'
        ]);
        DocumentType::create([
            'code' => '2',
            'label' => 'pasaporte'
        ]);

        // 8. Estado reclamo
        ClaimStatus::create([
            'name' => 'pendiente',
        ]);
        ClaimStatus::create([
            'name' => 'completo',
        ]);

        // 9. Tipo Reclamo
        ClaimType::create([
            'name' => 'reclamo',
        ]);

    }
}