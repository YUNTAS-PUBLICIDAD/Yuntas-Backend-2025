<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use App\Models\Template;
use App\Models\TemplateContent;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $detalleSource = LeadSource::firstOrCreate(['name' => 'Producto detalle']);

        // =========================
        // 🟢 TEMPLATE: DETALLE
        // =========================
        $templateDetalle = Template::updateOrCreate(
            ['lead_source_id' => $detalleSource->id],
            [
                'name' => 'Template Detalle Producto',
                'active' => true,
            ]
        );

        TemplateContent::updateOrCreate(
            [
                'template_id' => $templateDetalle->id,
                'channel' => 'whatsapp',
            ],
            [
                'content' => "📢 *Bienvenido a Yuntas Publicidad* 📢\n\n" .
                            "Gracias por su interés en nuestros productos. A continuación, los detalles del producto consultado:\n\n" .
                            "📝 *Producto Consultado:*\n" .
                            "• Nombre: *{{producto_nombre}}*\n" .
                            "• Descripción: {{descripcion}}\n\n" .
                            "📅 *Fecha y Hora de Consulta:*\n" .
                            "• Fecha: {{fecha}}\n" .
                            "• Hora: {{hora}}\n\n" .
                            "📧 *Información Adicional:*\n" .
                            "Le informamos que recibirá un correo a *{{email}}* con más detalles sobre el producto consultado. Le recomendamos revisar su bandeja de entrada.\n" .
                            "Si tiene alguna otra consulta o desea más información, no dude en contactarnos.\n\n" .
                            "¡Gracias por elegirnos!\n\n" .
                            "Atentamente,\n" .
                            "*Yuntas Publicidad*",
                'variables' => [
                    'producto_nombre',
                    'descripcion',
                    'fecha',
                    'hora',
                    'email'
                ],
                'image_url' => null,
                'active' => true,
            ]
        );
        TemplateContent::updateOrCreate(
            [
                'template_id' => $templateDetalle->id,
                'channel' => 'email',
            ],
            [
                'subject' => 'Detalle del producto consultado',
                'content' => "📢 <h1>Bienvenido a Yuntas Publicidad</h1>\n\n" .
                            "Gracias por su interés en nuestros productos. A continuación, los detalles del producto consultado:\n\n" .
                            "📝 <h2>Producto Consultado</h2>\n" .
                            "• Nombre: <b>{{producto_nombre}}</b>\n" .
                            "• Descripción: {{descripcion}}\n\n" .
                            "📅 <h2>Fecha y Hora de Consulta</h2>\n" .
                            "• Fecha: {{fecha}}\n" .
                            "• Hora: {{hora}}\n\n" .
                            "📧 <h2>Información Adicional</h2>\n" .
                            "Le informamos que recibirá un correo a <b>{{email}}</b> con más detalles sobre el producto consultado. Le recomendamos revisar su bandeja de entrada.\n" .
                            "Si tiene alguna otra consulta o desea más información, no dude en contactarnos.\n\n" .
                            "¡Gracias por elegirnos!\n\n" .
                            "Atentamente,\n" .
                            "*Yuntas Publicidad*",
                'variables' => [
                    'nombre',
                    'producto_nombre',
                    'descripcion',
                    'fecha',
                    'hora',
                    'email'
                ],
                'image_url' => null,
                'active' => true,
            ]
        );
    }
}