<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Template;
use App\Models\TemplateContent;
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
        ProductContentSlot::firstOrCreate(['name' => 'Especificaciones']);
        ProductContentSlot::firstOrCreate(['name' => 'Beneficios']);

        BlogContentSlot::firstOrCreate(['name' => 'Descripciones']);
        BlogContentSlot::firstOrCreate(['name' => 'Beneficios']);
        BlogContentSlot::firstOrCreate(['name' => 'Testimonios']);

        // 5. Fuentes de Leads
     $inicioSource =   LeadSource::firstOrCreate(['name' => 'Inicio']);
      $productosSource =  LeadSource::firstOrCreate(['name' => 'Productos']);
       $detalleSource = LeadSource::firstOrCreate(['name' => 'Producto detalle']);
       $adminSource = LeadSource::firstOrCreate(['name' => 'Administración']);

        // 6. Plantillas de Whatsapp Popup
        // $inicioSource = LeadSource::where('name', 'Inicio')->first();
        // $productosSource = LeadSource::where('name', 'Productos')->first();
        // $detalleSource = LeadSource::where('name', 'Producto detalle')->first();
        // WhatsappPopup::updateOrCreate(
        //     ['lead_source_id' => $inicioSource->id],
        //     [
        //         'nombre' => 'Popup Inicio',
        //         'mensaje' => "👋 ¡Bienvenido(a) a *Yuntas Publicidad*!\n\n" .
        //                      "Hola *{nombre}*, gracias por escribirnos.\n\n" .
        //                      "Brindamos *servicios publicitarios personalizados para hacer crecer tu negocio.*\n\n" .
        //                      "📌 *Puedes consultarnos sobre:*\n" .
        //                      "• Productos publicitarios\n" .
        //                      "• Cotizaciones\n\n" .
        //                      "¡Estamos listos para ayudarte! 😊",
        //         'variables' => ['nombre'],
        //         'imagen_url' => 'storage/plantillas/yuntas-bienvenida.webp',
        //         'activo' => true,
        //     ]
        // );
        // WhatsappPopup::firstOrCreate(
        //     ['lead_source_id' => $productosSource->id],
        //     [
        //         'nombre' => 'Popup Productos',
        //         'mensaje' => "📦 *¡Hola {nombre}!* 📦\n\n" .
        //                     "Veo que estás explorando nuestro catálogo de productos publicitarios. ¡Excelente elección!\n\n" .
        //                     "✨ *Nuestros Productos*\n" .
        //                     "Contamos con una amplia variedad de artículos promocionales y publicitarios de alta calidad:\n" .
        //                     "• Pantallas LED\n" .
        //                     "• Proyectores holográficos 3D\n" .
        //                     "• Letreros luminosos\n" .
        //                     "• Letreros acrílicos\n" .
        //                     "• Artículos promocionales\n\n" .
        //                     "💼 *¿Necesitas asesoría?*\n" .
        //                     "Nuestro equipo está listo para ayudarte a encontrar el producto perfecto para tu marca.\n\n" .
        //                     "📧 *Próximos pasos*\n" .
        //                     "Te enviaremos información detallada de nuestros productos a tu correo.\n\n" .
        //                     "Si tienes alguna consulta específica, ¡escríbenos! Estamos para ayudarte.\n\n" .
        //                     "Atentamente,\n" .
        //                     "*Yuntas Publicidad* 🎨",
        //         'variables' => ['nombre'],
        //         'imagen_url' => null,
        //         'activo' => true,
        //     ]
        // );
        // WhatsappPopup::firstOrCreate(
        //     ['lead_source_id' => $detalleSource->id],
        //     [
        //         'nombre' => 'Popup Producto detalle',
        //         'mensaje' => "📢 *Bienvenido a Yuntas Publicidad* 📢\n\n" .
        //                     "Gracias por su interés en nuestros productos. A continuación, los detalles del producto consultado:\n\n" .
        //                     "📝 *Producto Consultado:*\n" .
        //                     "• Nombre: *{producto_nombre}*\n" .
        //                     "• Descripción: {descripcion}\n\n" .
        //                     "📅 *Fecha y Hora de Consulta:*\n" .
        //                     "• Fecha: {fecha}\n" .
        //                     "• Hora: {hora}\n\n" .
        //                     "📧 *Información Adicional:*\n" .
        //                     "Le informamos que recibirá un correo a *{email}* con más detalles sobre el producto consultado. Le recomendamos revisar su bandeja de entrada.\n" .
        //                     "Si tiene alguna otra consulta o desea más información, no dude en contactarnos.\n\n" .
        //                     "¡Gracias por elegirnos!\n\n" .
        //                     "Atentamente,\n" .
        //                     "*Yuntas Publicidad*",
        //         'variables' => ['producto_nombre', 'descripcion', 'fecha', 'hora', 'email'],
        //         'imagen_url' => null,
        //         'activo' => true,
        //     ]
        // );

        // $product = Product::firstOrCreate(
        //   ['slug' => 'producto-demo'],
        //   [
        //     'name' => 'Producto Demo',
        //     'hero_title' => 'Producto Demo Hero',
        //     'description' => 'Description de prueba',
        //     'price' => 100,
        //     'status' => 'active',
        //     'meta_title' => 'Producto Demo',
        //     'meta_description' => 'Meta descripción demo',
        //     'keywords' => ['demo', 'producto']
        //   ]
        // );

        // $templateProducto = Template::updateOrCreate(
        //   [
        //     'product_id' => $product->id,
        //     // 'lead_source_id' => $detalleSource->id
        //     'lead_source_id' => null
        //   ],
        //   [
        //     'name' => 'Secuencias Producto ' . $product->id,
        //     'active' => true
        //   ]
        // );

//         // STEP 0
//         TemplateContent::updateOrCreate(
//           [
//             'template_id' => $templateProducto->id,
//             'channel' => 'email',
//             // 'step' => 0
//           ],
//           [
// 'subject' => 'Gracias por tu interés',
// 'content' => "Hola {{nombre}},\n\nGracias por tu interés en *{{producto_nombre}}*.\n\n{{descripcion}}\n\n📅 Registrado el {{fecha}} a las {{hora}}.\n\nEn breve uno de nuestros asesores te contactará con más información.\n\nSi tienes alguna duda, puedes responder este correo.\n\nSaludos,\nEquipo de ventas",
//         'variables' => ['nombre', 'producto_nombre'],
//         'active' => true,
//           ]
//         );

//         // STEP 1
//         TemplateContent::updateOrCreate(
//           [
// 'template_id'=> $templateProducto->id,
// 'channel' => 'email',
// // 'step' => 1
//           ],
//           [
//             'subject' => 'Gracias por tu interés',
//         'content' => "Hola {{nombre}}, gracias por interesarte en {{producto_nombre}}",
//         'variables' => ['nombre', 'producto_nombre'],
//         'active' => true,
//           ]
//         );

//         TemplateContent::updateOrCreate(
//           [
//             'template_id' => $templateProducto->id,
//             'channel' => 'email',
//             // 'step' => 2
//           ],
//           [
//             'subject' => 'Seguimiento',
//             'content' => 'Hola {{nombre}}, seguimos atentos a tu interés',
//             'variables' => ['nombre'],
//             'active' => true,
//           ]
//         );

// =========================
        // 🟢 TEMPLATE: INICIO
        // =========================
        // $templateInicio = Template::updateOrCreate(
        //     ['lead_source_id' => $inicioSource->id],
        //     [
        //         'name' => 'Template Inicio',
        //         'active' => true,
        //     ]
        // );

        // TemplateContent::updateOrCreate(
        //     [
        //         'template_id' => $templateInicio->id,
        //         'channel' => 'whatsapp',
        //     ],
        //     [
        //         'content' => "👋 ¡Bienvenido(a) a *Yuntas Publicidad*!\n\nHola {{nombre}}, gracias por escribirnos.",
        //         'variables' => ['nombre'],
        //         'image_url' => 'storage/plantillas/yuntas-bienvenida.webp',
        //         'active' => true,
        //     ]
        // );

        // =========================
        // 🟢 TEMPLATE: PRODUCTOS
        // =========================
        $templateProductos = Template::updateOrCreate(
            ['lead_source_id' => $productosSource->id],
            [
                'name' => 'Template Productos',
                'active' => true,
            ]
        );

        TemplateContent::updateOrCreate(
            [
                'template_id' => $templateProductos->id,
                'channel' => 'whatsapp',
            ],
            [
                'content' => "📦 Hola {{nombre}}, mira nuestros productos...",
                'variables' => ['nombre'],
                'image_url' => null,
                'active' => true,
            ]
        );

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
                'content' => "📢 Producto: {{producto_nombre}}\nDescripción: {{descripcion}}\nFecha: {{fecha}}",
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

//         TemplateContent::updateOrCreate(
//     [
//         'template_id' => $templateInicio->id,
//         'channel' => 'email',
//     ],
//     [
//         'subject' => 'Bienvenido a Yuntas Publicidad',
//         'content' => "Hola {{nombre}},\n\nGracias por contactarnos. En Yuntas Publicidad te ayudamos a hacer crecer tu negocio.\n\nPronto nos comunicaremos contigo.",
//         'variables' => ['nombre'],
//         'image_url' => null,
//         'active' => true,
//     ]
// );

TemplateContent::updateOrCreate(
    [
        'template_id' => $templateProductos->id,
        'channel' => 'email',
    ],
    [
        'subject' => 'Nuestros productos para tu negocio',
        'content' => "Hola {{nombre}},\n\nTenemos una variedad de productos publicitarios ideales para ti.\n\nSi deseas más información, responde este correo.",
        'variables' => ['nombre'],
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
        'content' => "Hola {{nombre}},\n\nProducto: {{producto_nombre}}\nDescripción: {{descripcion}}\nFecha: {{fecha}}\nHora: {{hora}}\n\nNos estaremos comunicando contigo.",
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

        // 7. Documento ID
        DocumentType::firstOrCreate(['code' => '1','label' => 'dni']);
        DocumentType::firstOrCreate(['code' => '2','label' => 'pasaporte']);

        // 8. Estado reclamo
        ClaimStatus::firstOrCreate(['name' => 'pendiente']);
        ClaimStatus::firstOrCreate(['name' => 'completo']);

        // 9. Tipo Reclamo
        ClaimType::firstOrCreate(['name' => 'reclamo']);

        // Chatbot
        $this->call(ChatbotSeeder::class);

        // Products
        $this->call(ProductSeeder::class);
    }
}
