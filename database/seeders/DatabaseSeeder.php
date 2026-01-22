<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\LeadSource;
use App\Models\WhatsappPopup;
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
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

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
        LeadSource::firstOrCreate(['name' => 'Inicio']);
        LeadSource::firstOrCreate(['name' => 'Productos']);
        LeadSource::firstOrCreate(['name' => 'Producto detalle']);
        LeadSource::firstOrCreate(['name' => 'Administración']);

        // 6. Plantillas de Whatsapp Popup
        WhatsappPopup::firstOrCreate([
            'lead_source_id' => 1, 
            'nombre' => 'Popup Inicio',
            'mensaje' => '¡Hola {nombre}! 👋 Gracias por tu interés en Yuntas Publicidad. ¿En qué podemos ayudarte hoy?',
            'variables' => json_encode(['nombre']),
            'activo' => true,
            ]
        );
        WhatsappPopup::firstOrCreate([
            'lead_source_id' => 2, 
            'nombre' => 'Popup Productos',
            'mensaje' => 'Hola {nombre}, somos Yuntas Publicidad. Veo que te interesan nuestros productos 📦. ¿Necesitas información específica de alguno?',
            'variables' => json_encode(['nombre']),
            'activo' => true,
            ]
        );
        WhatsappPopup::firstOrCreate([
            'lead_source_id' => 3, 
            'nombre' => 'Popup Producto detalle',
            'mensaje' => "📢 *Bienvenido a Yuntas Publicidad* 📢\n\n" .
                        "Gracias por su interés en nuestros productos. A continuación, los detalles del producto consultado:\n\n" .
                        "📝 *Producto Consultado:*\n" .
                        "• Nombre: *{producto_nombre}*\n" .
                        "• Descripción: {descripcion}\n\n" .
                        "📅 *Fecha y Hora de Consulta:*\n" .
                        "• Fecha: {fecha}\n" .
                        "• Hora: {hora}\n\n" .
                        "📧 *Información Adicional:*\n" .
                        "Le informamos que recibirá un correo a *{email}* con más detalles sobre el producto consultado. Le recomendamos revisar su bandeja de entrada.\n" .
                        "Si tiene alguna otra consulta o desea más información, no dude en contactarnos.\n\n" .
                        "¡Gracias por elegirnos!\n\n" .
                        "Atentamente,\n" .
                        "*Yuntas Publicidad*",
            'variables' => json_encode(['producto_nombre', 'descripcion', 'fecha', 'hora', 'email']),
            'activo' => true,
            ]
        );


        // 7. Documento ID
        DocumentType::firstOrCreate(['code' => '1','label' => 'dni']);
        DocumentType::firstOrCreate(['code' => '2','label' => 'pasaporte']);

        // 8. Estado reclamo
        ClaimStatus::firstOrCreate(['name' => 'pendiente']);
        ClaimStatus::firstOrCreate(['name' => 'completo']);

        // 9. Tipo Reclamo
        ClaimType::firstOrCreate(['name' => 'reclamo']);

    }
}