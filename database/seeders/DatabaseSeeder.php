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
        $inicioSource = LeadSource::where('name', 'Inicio')->first();
        $productosSource = LeadSource::where('name', 'Productos')->first();
        $detalleSource = LeadSource::where('name', 'Producto detalle')->first();
        WhatsappPopup::firstOrCreate(
            ['lead_source_id' => $inicioSource->id],    
            [
                'nombre' => 'Popup Inicio',
                 'mensaje' => "👋 *¡Bienvenido a Yuntas Publicidad!* 👋\n\n" .
                            "Hola *{nombre}*, gracias por visitarnos y mostrar interés en nuestros servicios.\n\n" .
                            "🎯 *Somos tu aliado en publicidad*\n" .
                            "Nos especializamos en soluciones publicitarias personalizadas que ayudan a destacar tu marca.\n\n" .
                            "💡 *¿En qué podemos ayudarte?*\n" .
                            "• Productos publicitarios personalizados\n" .
                            "• Cotizaciones sin compromiso\n\n" .
                            "📧 *Mantente informado*\n" .
                            "Pronto recibirás información detallada en tu correo.\n\n" .
                            "Estamos aquí para resolver todas tus dudas. ¡No dudes en escribirnos!\n\n" .
                            "Saludos cordiales,\n" .
                            "*Yuntas Publicidad* ✨",
                'variables' => ['nombre'],
                'imagen_url' => null,
                'activo' => true,
            ]
        );
        WhatsappPopup::firstOrCreate(
            ['lead_source_id' => $productosSource->id],    
            [
                'nombre' => 'Popup Productos',
                'mensaje' => "📦 *¡Hola {nombre}!* 📦\n\n" .
                            "Veo que estás explorando nuestro catálogo de productos publicitarios. ¡Excelente elección!\n\n" .
                            "✨ *Nuestros Productos*\n" .
                            "Contamos con una amplia variedad de artículos promocionales y publicitarios de alta calidad:\n" .
                            "• Pantallas LED\n" .
                            "• Proyectores holográficos 3D\n" .
                            "• Letreros luminosos\n" .
                            "• Letreros acrílicos\n" .
                            "• Artículos promocionales\n\n" .
                            "💼 *¿Necesitas asesoría?*\n" .
                            "Nuestro equipo está listo para ayudarte a encontrar el producto perfecto para tu marca.\n\n" .
                            "📧 *Próximos pasos*\n" .
                            "Te enviaremos información detallada de nuestros productos a tu correo.\n\n" .
                            "Si tienes alguna consulta específica, ¡escríbenos! Estamos para ayudarte.\n\n" .
                            "Atentamente,\n" .
                            "*Yuntas Publicidad* 🎨",
                'variables' => ['nombre'],
                'imagen_url' => null,
                'activo' => true,
            ]
        );
        WhatsappPopup::firstOrCreate(
            ['lead_source_id' => $detalleSource->id],    
            [
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
                'variables' => ['producto_nombre', 'descripcion', 'fecha', 'hora', 'email'],
                'imagen_url' => null,
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