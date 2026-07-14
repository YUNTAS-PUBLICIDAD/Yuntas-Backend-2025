<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tablas
        DB::table('role_has_permissions')->delete();
        Permission::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | Crear permisos
        |--------------------------------------------------------------------------
        */

        $modules = [

            'blogs' => [
                'crear',
                'editar',
                'eliminar',
                'publicar',
            ],

            'productos' => [
                'crear',
                'editar',
                'eliminar',
                'publicar',
            ],

            'popups' => [
                'crear',
                'editar',
                'eliminar',
                'actualizar',
            ],

            'plantillas' => [
                'crear',
                'editar',
                'eliminar',
            ],

        ];

        foreach ($modules as $module => $actions) {

            foreach ($actions as $action) {

                Permission::create([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Obtener Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::where('name', 'admin')->first();
        $marketing = Role::where('name', 'marketing')->first();
        $diseño = Role::where('name', 'diseño')->first();

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if ($admin) {

            $admin->permissions()->sync(
                Permission::pluck('id')->toArray()
            );

        }

        /*
        |--------------------------------------------------------------------------
        | MARKETING
        |--------------------------------------------------------------------------
        */

        if ($marketing) {

            $marketing->permissions()->sync(
                Permission::whereIn('name', [

                    'blogs.crear',
                    'blogs.editar',
                    'blogs.eliminar',

                    'productos.crear',
                    'productos.editar',
                    'productos.eliminar',

                    'popups.crear',
                    'popups.editar',
                    'popups.eliminar',

                    'plantillas.crear',
                    'plantillas.editar',
                    'plantillas.eliminar',

                ])->pluck('id')->toArray()

            );

        }

        /*
        |--------------------------------------------------------------------------
        | DISEÑO
        |--------------------------------------------------------------------------
        */

        if ($diseño) {

            $diseño->permissions()->sync(
                Permission::whereIn('name', [

                    'blogs.editar',
                    'blogs.publicar',

                    'productos.editar',
                    'productos.publicar',

                    'popups.editar',
                    'popups.actualizar',

                    'plantillas.editar',

                ])->pluck('id')->toArray()

            );

        }
    }
}