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
        $marketingRole = Role::firstOrCreate(['name' => 'marketing']);
        $ventasRole = Role::firstOrCreate(['name' => 'ventas']);

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
        ImageSlot::firstOrCreate(['module' => 'blogs', 'name' => 'List']);
        ImageSlot::firstOrCreate(['module' => 'blogs', 'name' => 'Hero']);
        ImageSlot::firstOrCreate(['module' => 'blogs', 'name' => 'Desc']);
        ImageSlot::firstOrCreate(['module' => 'blogs', 'name' => 'Benefits']);
        ImageSlot::firstOrCreate(['module' => 'blogs', 'name' => 'Testimonial']);
        ImageSlot::firstOrCreate(['module' => 'products', 'name' => 'List']);
        ImageSlot::firstOrCreate(['module' => 'products', 'name' => 'Hero']);
        ImageSlot::firstOrCreate(['module' => 'products', 'name' => 'Specs']);
        ImageSlot::firstOrCreate(['module' => 'products', 'name' => 'Benefits']);
        ImageSlot::firstOrCreate(['module' => 'products', 'name' => 'Popups']);

        // 4. Slots de Contenido
        ProductContentSlot::firstOrCreate(['name' => 'Especificaciones'], ['data_type' => 'list', 'position' => 1]);
        ProductContentSlot::firstOrCreate(['name' => 'Beneficios'], ['data_type' => 'list', 'position' => 2]);

        BlogContentSlot::firstOrCreate(['name' => 'Descripciones'], ['data_type' => 'text', 'position' => 1]);
        BlogContentSlot::firstOrCreate(['name' => 'Beneficios'], ['data_type' => 'list', 'position' => 2]);
        BlogContentSlot::firstOrCreate(['name' => 'Testimonios'], ['data_type' => 'text', 'position' => 3]);

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
                'mensaje' => "¡Bienvenido/a a Yuntas!\n\n" .
                            "Gracias por registrarte, *{nombre}*. Nos alegra tenerte con nosotros y que formes parte de nuestra comunidad.\n\n" .
                            "Desde ahora tendrás acceso a información sobre nuestros productos, novedades y soluciones en letreros acrílicos y señalización diseñadas para potenciar la imagen de tu negocio con un estilo moderno, profesional y duradero.\n\n" .
                            "Si tienes alguna consulta o necesitas asesoría personalizada, no dudes en escribirnos. Estaremos encantados de ayudarte a encontrar la mejor opción para tu proyecto.\n\n" .
                            "Gracias por confiar en Yuntas.\n" .
                            "Tu marca merece destacar.\n\n" .
                            "*Equipo Yuntas*",
                'variables' => ['nombre'],
                'imagen_url' => 'storage/plantillas/yuntas-bienvenida.webp',
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