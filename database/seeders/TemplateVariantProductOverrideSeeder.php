<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TemplateVariantProductOverride;

class TemplateVariantProductOverrideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $overrides = [
            [
                'template_variant_id' => 1,
                'product_id' => 4,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉

Llévate nuestras barras Pixel LED a un precio especial por ser nuevo cliente.
Recuerda que este producto brinda una  iluminación dinámica para atraer más miradas. 
¡No te quedes sin el tuyo! 😱🔥
Responde “ACTIVAR” para obtener tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 4,
                'subject' => '🚀 Lleva tu negocio al siguiente nivel con 10% OFF en Letreros Neon LED ',
                'content' => '<p>Hola, {{nombre}} 😊</p>

<p>Las Barras Pixel LED no solo decoran, también ayudan a que tu negocio destaque y genere una imagen más moderna y atractiva para tus clientes.</p>
<p>Además, son personalizables en:</p>
<p>    color, diseño y tamaño, </p>
<p>Ideales para locales, eventos, cafeterías, tiendas y espacios que buscan diferenciarse visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un <strong>10% OFF</strong> en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR %',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 4,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel? 🚀

La gran ventaja de nuestras barras Pixel LED es que le brinda una nueva percepción a tu negocio “Más interactivo” “Más versátil y atractivo” 
Si cierras la compra hoy te lo llevas con un 15% de descuento 🎉
¿Estás listo para pasar al siguiente nivel? 😎📈
Responde “LO QUIERO” para continuar',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 4,
                'subject' => '⏰ Últimas horas de 15% OFF para hacer destacar tu negocio ✨',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestras Barras Pixel LED.</p>
<p>Hoy, muchos negocios utilizan elementos visuales llamativos para atraer más miradas, generar recordación y destacar frente a la competencia. Un espacio que impacta visualmente, se queda en la mente del cliente. ✨</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'OBTENER %',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 4,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Barras Pixel LED y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 4,
                'subject' => '🎉 ¡Aprovecha 2x1 en Barras Pixel LED por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva 2x1 en Barras Pixel LED  y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios ',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 16,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉

Llévate nuestros monitores de publicidad digital a un precio especial por ser nuevo cliente. 
Recuerda que este producto permite mostrar contenido dinámico y promociones en tiempo real que atraen más clientes.  
¡No te quedes sin el tuyo! 😱🔥
Responde “ACTIVAR” para obtener tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 16,
                'subject' => '🚀 Lleva tu negocio al siguiente nivel con 10% OFF en Monitores de publicidad digital',
                'content' => '<p>Hola {{nombre}} </p>
<p>Sabemos que destacar hoy en día no es fácil, pero con nuestros Monitores de publicidad digital puedes lograrlo de manera impactante.</p>
<p>Por tiempo limitado, queremos ofrecerte un <strong>🔥 10% DE DESCUENTO 🔥</strong> para que implementes esta tecnología en tu negocio, con equipos de alta calidad y acompañamiento en la elección ideal según tu espacio. </p>
<p>Imagina tener contenido en movimiento flotando frente a tus clientes, generando curiosidad y aumentando tu alcance visual. </p>
<p><strong>No dejes pasar esta oportunidad de innovar.</strong></p>
<p><strong>Contáctanos y aprovecha este beneficio exclusivo ✨</strong></p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 16,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel? 🚀

La gran ventaja de nuestros monitores de publicidad digital es que le brinda una nueva percepción a tu negocio “Más visibilidad” “Más interacción e innovación” 
Si cierras la compra hoy te lo llevas con un 15% de descuento. 
¿Estás listo para pasar al siguiente nivel? 📈😎
Responde "LO QUIERO" para continuar.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 16,
                'subject' => '⏰ Últimas horas para impactar con Monitores de publicidad digital ✨',
                'content' => '<p>Hola {{nombre}}</p>
<p>Todavía estás a tiempo de aprovechar el <strong>15% OFF</strong> en nuestros Monitores de publicidad digital.</p>
<p>Hoy, muchos negocios utilizan Monitores de publicidad digital para captar más atención, generar experiencias visuales sorprendentes y diferenciarse de la competencia con tecnología innovadora. </p>
<p>Un negocio que impacta visualmente, permanece en la mente del cliente. ✨ </p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p><strong>Activa tu descuento dandole click al botón debajo ⬇️</strong></p>
<p><strong✨¡No te lo puedes perder! 🔥</strong></p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios ',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 16,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Monitores de Publicidad Digital y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 16,
                'subject' => '🎉 ¡2x1 en Monitores de publicidad digital por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Monitores de publicidad digital</strong> y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios ',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 10,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉

Llévate nuestras letras doradas y plateadas a un precio especial por ser nuevo cliente 
Recuerda que este producto brinda gran impacto visual para tu negocio 
¡No te quedes sin el tuyo! 😱🔥
Responde “ACTIVAR” para obtener tu descuento',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 10,
                'subject' => '🚀 Lleva tu negocio al siguiente nivel con 10% OFF en Letras Doradas y Plateadas',
                'content' => '<p>Hola {{nombre}} </p>
<p>Sabemos que destacar hoy en día no es fácil, pero con nuestros Letras Doradas y Plateadas puedes lograrlo de manera impactante.</p>
<p>Por tiempo limitado, queremos ofrecerte un <strong>🔥 10% DE DESCUENTO 🔥</strong> para que implementes esta tecnología en tu negocio, con equipos de alta calidad y acompañamiento en la elección ideal según tu espacio. </p>
<p>Imagina tener contenido en movimiento flotando frente a tus clientes, generando curiosidad y aumentando tu alcance visual. </p>
<p><strong>No dejes pasar esta oportunidad de innovar.</strong></p>
<p><strong>Contáctanos y aprovecha este beneficio exclusivo ✨</strong></p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 10,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel?🚀

La gran ventaja de nuestras letras doradas y plateadas es que le brinda una nueva percepción a tu negocio "Más elegante" "Más visible y moderno". 
Si cierras la compra hoy te lo llevas con un 15% de descuento. 
¿Estás listo para pasar al siguiente nivel? 📈 😎
Responde "LO QUIERO" para continuar.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 10,
                'subject' => '⏰ Últimas horas  de 15% OFF para impactar con Letras Doradas y Plateadas✨',
                'content' => '<p>Hola {{nombre}}</p>
<p>Todavía estás a tiempo de aprovechar el <strong>15% OFF</strong> en nuestros Letras Doradas y Plateadas.</p>
<p>Hoy, muchos negocios utilizan Letras Doradas y Plateadas para captar más atención, generar experiencias visuales sorprendentes y diferenciarse de la competencia con tecnología innovadora. </p>
<p>Un negocio que impacta visualmente, permanece en la mente del cliente. ✨ </p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p><strong>Activa tu descuento dandole click al botón debajo ⬇️</strong></p>
<p><strong>✨¡No te lo puedes perder! 🔥</strong></p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 10,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Letras Doradas y Plateadas y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 10,
                'subject' => '🎉 ¡Aprovecha este 2x1 en Letras Doradas y Plateadas por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva 2x1 en Letras Doradas y Plateadas y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios ',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 12,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉

Llévate nuestros letreros luminosos a un precio especial por ser nuevo cliente 
Recuerda que este producto este producto hace que tu negocio sea visible día y noche, atrayendo más clientes desde cualquier distancia 
¡No te quedes sin el tuyo! 😱🔥
Responde “ACTIVAR” para obtener tu descuento',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 12,
                'subject' => '🔥 Dale más presencia a tu negocio con 10% OFF en Letreros Luminosos 💡 ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Los Letreros Luminosos no solo decoran, también ayudan a que cualquier espacio luzca más profesional, atractivo y fácil de recordar para tus clientes. </p>
<p>Además, son personalizables en:</p>
<p>🎨 Color   |   ✏️ Diseño   |  📏 Tamaño</p>
<p>Ideales para fachadas, restaurantes, tiendas, barberías, minimarkets y negocios que quieren resaltar visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un 10% OFF en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR 10%” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 12,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel?🚀

La gran ventaja de nuestros letreros luminosos es que le brinda una nueva percepción a tu negocio “Más iluminado” “Más presencia y impacto”
Si cierras la compra hoy te lo llevas con un 15% de descuento. 
¿Estás listo para pasar al siguiente nivel? 📈😎
Responde "LO QUIERO" para continuar.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 12,
                'subject' => '⚡ Últimas horas para obtener tu Letrero Luminoso con 15% OFF 🌟 ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestros Letreros Luminosos.</p>
<p>Hoy, muchos negocios utilizan Letreros Luminosos para atraer más miradas, generar mayor impacto visual y transmitir una imagen mucho más moderna frente a la competencia. Un negocio que destaca visualmente siempre deja huella. 💫 </p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios ',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 12,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Letreros Luminosos y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 12,
                'subject' => '🎉 ¡2x1 en Letreros Luminosos por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Letreros Luminosos</strong> y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 14,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉

Llévate nuestras pantalla LED con precio especial a un precio especial por ser nuevo cliente 
Recuerda que este producto es ideal para mostrar promociones, videos y contenido atractivo que capta la atención al instante.
¡No te quedes sin el tuyo! 😱🔥
Responde “ACTIVAR” para obtener tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 14,
                'subject' => '🚀 Lleva tu negocio al siguiente nivel con 10% OFF en Pantallas LED',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Las pantallas LED no solo decoran, también ayudan a que tu negocio destaque y genere una imagen más moderna y atractiva para tus clientes.</p>
<p>Además, son personalizables en:</p>
<p>color, diseño y tamaño,</p>
<p>Ideales para locales, eventos, cafeterías, tiendas y espacios que buscan diferenciarse visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un 10% OFF en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR %',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 14,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel? 🚀

La gran ventaja de nuestras pantallas LED es que le brinda una nueva percepción a tu negocio “Más alcance” “Más visible y llamativo”
Si cierras la compra hoy te lo llevas con un 15% de descuento. 
¿Estás listo para pasar al siguiente nivel? 📈😎
Responde "LO QUIERO" para continuar.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 14,
                'subject' => '⏰ Últimas horas para hacer destacar tu negocio ✨',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestras Pantallas LED.</p>
<p>Hoy, muchos negocios utilizan elementos visuales llamativos para atraer más miradas, generar recordación y destacar frente a la competencia. Un espacio que impacta visualmente, se queda en la mente del cliente. ✨</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'OBTENER %',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 14,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Pantallas LED y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 14,
                'subject' => '🎉 ¡2x1 en Pantallas LED por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Pantallas LED</strong> y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 9,
                'content' => 'Hola {{nombre}}
Solo por hoy 10% de descuento en tu primera compra. 🔥 

Llévate nuestros menús board a un precio especial por ser nuevo cliente.  
Recuerda que este producto organiza tu local y hace que tus clientes vean tus precios al instante. 
¡No te quedes sin el tuyo.! 😱 🔥
Responde “ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 9,
                'subject' => '🚀 LLEVA TU NEGOCIO AL SIGUIETE NIVEL CON ESTE 10% OFF EN MENU BOARDS',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Los Menú Boards no solo decoran, también ayudan a que tu negocio destaque y genere una imagen más moderna y atractiva para tus clientes.</p>
<p>Además, son personalizables en:</p>
<p>color, diseño y tamaño, </p>
<p>Ideales para locales, eventos, cafeterías, tiendas y espacios que buscan diferenciarse visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un <strong>10% OFF</strong> en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 9,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel?  🚀

La gran ventaja de nuestros menús board es que organizan tu local y hacen que tus clientes decidan más rápido qué comprar.  
Si cierras la compra hoy te llevas un 15% de descuento.  
¿Estás listo para pasar al siguiente nivel? 📈😎
 Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 9,
                'subject' => '⏰ ÚLTIMAS HORAS DE 15% OFF 🚨 PARA HACEER DESTACAR A TU NEGOCIO CON MENU BOARDS ✨',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestros Menú Boards.</p>
<p>Hoy, muchos negocios utilizan elementos visuales llamativos para atraer más miradas, generar recordación y destacar frente a la competencia. Un espacio que impacta visualmente, se queda en la mente del cliente. ✨</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'OBTENER %',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 9,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Menú Boards y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 9,
                'subject' => '🎉APROVECHA ESTE 2X1 EN MENU BOARD Y TRANSFORMA TU NEGOCIO',
                'content' => '<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva 2x1 en Menú boards  y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 8,
                'content' => 'Hola {{nombre}}<p>
¡Solo por hoy 10% de descuento en tu primera compra.! 🔥

Llévate nuestras letras pintadas en MDF a un precio especial por ser nuevo cliente.  
Recuerda que este producto le da identidad a tu marca y hace que tu fachada destaque. 
No te quedes sin el tuyo. 😱🔥  
Responde “ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 8,
                'subject' => '🚀 Lleva tu negocio al siguiente nivel con 10% OFF en Letras pintadas en MDF',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Las Letras pintadas en MDF no solo decoran, también ayudan a que tu negocio destaque y genere una imagen más moderna y atractiva para tus clientes.</p>
<p>Además, son personalizables en:</p>
<p>    color, diseño y tamaño, </p>
<p>Ideales para locales, eventos, cafeterías, tiendas y espacios que buscan diferenciarse visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un <strong>10% OFF</strong> en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 8,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel?  🚀

La gran ventaja de nuestras letras pintadas en MDF es que le dan personalidad a tu marca y tu fachada se ve 100% profesional.  
Si cierras la compra hoy te llevas un 15% de descuento.  
¿Estás listo para pasar al siguiente nivel? 📈😎
 Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 8,
                'subject' => '⏰ Últimas horas para hacer destacar tu negocio ✨',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestras Letras pintadas en MDF.</p>
<p>Hoy, muchos negocios utilizan elementos visuales llamativos para atraer más miradas, generar recordación y destacar frente a la competencia. Un espacio que impacta visualmente, se queda en la mente del cliente. ✨</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'OBTENER %',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 8,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Letras Pintadas en MDF y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 8,
                'subject' => '🎉 ¡2x1 en Letras pintadas en MDF por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva 2x1 en Letras pintadas en MDF y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 5,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra.! 🎉 🔥  

Llévate nuestros neones LED a un precio especial por ser nuevo cliente.  
Recuerda: este producto atrae todas las miradas de día y de noche con luz propia. 
No te quedes sin el tuyo. 😱🔥
Responde “ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 5,
                'subject' => '🔥 Renueva tu espacio con 10% OFF en Neón LED ✨ ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>El Neón LED no solo decora, también ayuda a transformar cualquier espacio en un ambiente más moderno, profesional y visualmente atractivo para tus clientes. </p>
<p>Además, son personalizables en:</p>
<p>🎨 Color   |   ✏️ Diseño   |  📏 Tamaño</p>
<p>Ideales para tiendas, oficinas, vitrinas, restaurantes, recepciones y negocios que buscan destacar visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un 10% OFF en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
            ],
            [
                'template_variant_id' => 3,
                'product_id' => 5,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel?  🚀

La gran ventaja de nuestros neones LED es que tu local brilla 24/7 y se vuelve el punto de fotos de tus clientes.  
Si cierras la compra hoy te llevas un 15% de descuento.  
¿Estás listo para pasar al siguiente nivel? 📈😎 
Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 5,
                'subject' => '⏰ Últimas horas para aprovechar el 15% OFF en Neón LED ✨ ',
                'content' => '<p>Hola, {{nombre}} 😊</p>

<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestras Neón LED.</p>
<p>Hoy, muchos negocios utilizan Neón LED para reforzar su imagen, comunicar promociones y crear espacios más atractivos que capten la atención de sus clientes. Un diseño visual impactante siempre genera mayor recordación.🔥</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 5,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Neón LED y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 5,
                'subject' => '🎉 ¡2x1 en Neón LED por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Neón LED</strong> y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 7,
                'content' => 'Hola {{nombre}}
Solo por hoy 10% de descuento en tu primera compra. ✨🎉  

Llévate nuestros letreros acrílicos a un precio especial  por ser nuevo cliente.  
Recuerda que este producto da una imagen limpia, moderna y profesional a tu negocio. 
No te quedes sin el tuyo. 😱🔥
Responde “ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 7,
                'subject' => '🔥 Renueva tu espacio con 10% OFF en Letreros acrílicos ✨',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Los letreros acrílicos  no solo decoran, también ayudan a transformar cualquier espacio en un ambiente más moderno, profesional y visualmente atractivo para tus clientes. </p>
<p>Además, son personalizables en:</p>
<p>🎨 Color   |   ✏️ Diseño   |  📏 Tamaño</p>
<p>Ideales para tiendas, oficinas, vitrinas, restaurantes, recepciones y negocios que buscan destacar visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un 10% OFF en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 7,
                'content' => '¿Listo para que tu negocio destaque a otro nivel?  🚀
La gran ventaja de nuestros letreros acrílicos es que proyectan una imagen limpia, moderna y de alta gama al instante. 
Si cierras la compra hoy te llevas un 15% de descuento. 
¿Estás listo para pasar al siguiente nivel? 📈🔥
Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 7,
                'subject' => '⏰ Últimas horas para aprovechar el 15% OFF en Letreros acrílicos  ✨ ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestras Letreros acrílicos </p>
<p>Hoy, muchos negocios utilizan Letreros acrílicos  para reforzar su imagen, comunicar promociones y crear espacios más atractivos que capten la atención de sus clientes. Un diseño visual impactante siempre genera mayor recordación.🔥</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 7,
                'content' => '🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Letreros Acrílicos y paga solo 1. 😱
Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 7,
                'subject' => '🎉 ¡2x1 en Letreros acrílicos por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva 2x1 en Letreros acrílicos  y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p> ✨ Más impacto</p>
<p> ✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</p>
<p>Activa tu promoción haciendo clic en el botón de abajo. 👇</p>
<p>🔥 ¡No te lo puedes perder!</p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 11,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉✨

Llévate nuestras impresiones en vinilo a un precio especial por ser nuevo cliente.  
Recuerda que este producto viste tus vidrieras y paredes con tu marca a bajo costo. 
No te quedes sin el tuyo. 😱🔥 
Responde “ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 11,
                'subject' => '🔥 Renueva tu espacio con 10% OFF en Impresión en Vinilo ✨ ',
                'content' => '<p>Hola, {{nombre}} 😊</p>

<p>La Impresión en Vinilo no solo decora, también ayuda a transformar cualquier espacio en un ambiente más moderno, profesional y visualmente atractivo para tus clientes. </p>
<p>Además, son personalizables en:</p>
<p>🎨 Color   |   ✏️ Diseño   |  📏 Tamaño</p>
<p>Ideales para tiendas, oficinas, vitrinas, restaurantes, recepciones y negocios que buscan destacar visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un 10% OFF en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 11,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel? 🚀 

La gran ventaja de nuestras impresiones en vinilo es transformar cualquier pared o vidriera en publicidad que vende todo el día.  
Si cierras la compra hoy te llevas un 15% de descuento. 
¿Estás listo para pasar al siguiente nivel? 📈🚀
Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 11,
                'subject' => '⏰ Últimas horas para aprovechar el 15% OFF en Impresión en Vinilo ✨ ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestras  Impresiones en Vinilo.</p>
<p>Hoy, muchos negocios utilizan Impresión en Vinilo para reforzar su imagen, comunicar promociones y crear espacios más atractivos que capten la atención de sus clientes. Un diseño visual impactante siempre genera mayor recordación.🔥</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 11,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Impresiones en Vinilo y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 11,
                'subject' => '🎉 ¡2x1 en Impresión en Vinilo por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>

<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Impresión en Vinilo</strong> y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 15,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra!  🎉✨

Llévate nuestras mesas y sillas LED a un precio especial por ser nuevo cliente.  
Recuerda que este producto convierte tu local en un ambiente único que la gente quiere fotografiar. 
No te quedes sin el tuyo. 😱🔥
Responde ”ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 15,
                'subject' => '🔥 Renueva tu espacio con 10% OFF en Mesas y Sillas LED (Sillas Luminosas) ✨ ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Las Mesas y Sillas LED (Sillas Luminosas) no solo decoran, también ayudan a transformar cualquier espacio en un ambiente más moderno, profesional y visualmente atractivo para tus clientes. </p>
<p>Además, son personalizables en:</p>
<p>🎨 Color   |   ✏️ Diseño   |  📏 Tamaño</p>
<p>Ideales para tiendas, oficinas, vitrinas, restaurantes, recepciones y negocios que buscan destacar visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un 10% OFF en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 15,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel? 🚀

La gran ventaja de nuestras mesas y sillas LED es que crean un ambiente único que hace que la gente se quede más tiempo y gaste más. 
Si cierras la compra hoy te llevas un 15% de descuento.  
¿Estás listo para pasar al siguiente nivel? 📈😎
Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 15,
                'subject' => '⏰ Últimas horas para aprovechar el 15% OFF en Mesas y Sillas LED (Sillas Luminosas) ✨ ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestras Mesas y Sillas LED (Sillas Luminosas).</p>
<p>Hoy, muchos negocios utilizan Mesas y Sillas LED (Sillas Luminosas) para reforzar su imagen, comunicar promociones y crear espacios más atractivos que capten la atención de sus clientes. Un diseño visual impactante siempre genera mayor recordación.🔥</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 15,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Mesas y Sillas LED y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 15,
                'subject' => '🎉 ¡2x1 en Mesas y Sillas LED (Sillas Luminosas) por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Mesas y Sillas LED (Sillas Luminosas)</strong> y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 13,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉🔥

Llévate nuestros paneles Led electrónicos a un precio especial por ser nuevo cliente.
Este producto transforma cualquier espacio en un ambiente llamativo que atrae y retiene clientes. 
No te quedes sin el tuyo. 😱✨
Responde “ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 13,
                'subject' => '🚀 Lleva tu negocio al siguiente nivel con 10% OFF en Panel LED eléctricos',
                'content' => '<p>Hola {{nombre}} </p>
<p>Sabemos que destacar hoy en día no es fácil, pero con nuestros Panel LED eléctricos puedes lograrlo de manera impactante.</p>
<p>Por tiempo limitado, queremos ofrecerte un <strong>🔥 10% DE DESCUENTO 🔥</strong> para que implementes esta tecnología en tu negocio, con equipos de alta calidad y acompañamiento en la elección ideal según tu espacio. </p>
<p>Imagina tener contenido en movimiento flotando frente a tus clientes, generando curiosidad y aumentando tu alcance visual. </p>
<p><strong>No dejes pasar esta oportunidad de innovar.</strong></p>
<p><strong>Contáctanos y aprovecha este beneficio exclusivo ✨</strong></p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 13,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel? 🚀

La gran ventaja de nuestros paneles Led electrónicos es generar un ambiente envolvente que hace que las personas permanezcan más tiempo en tu espacio.
Si cierras la compra hoy te llevas un 15% de descuento.
¿Estás listo para pasar al siguiente nivel? 📈😎
Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 13,
                'subject' => '⏰ Últimas horas para impactar con Panel LED eléctricos✨',
                'content' => '<p>Hola {{nombre}}</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestros Panel LED eléctricos.</p>
<p>Hoy, muchos negocios utilizan Panel LED eléctricos para captar más atención, generar experiencias visuales sorprendentes y diferenciarse de la competencia con tecnología innovadora. </p>
<p>Un negocio que impacta visualmente, permanece en la mente del cliente. ✨ </p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p><strong>Activa tu descuento dandole click al botón debajo ⬇️</strong></p>
<p><strong>✨¡No te lo puedes perder! 🔥</strong></p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 13,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Paneles LED Electrónicos y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 13,
                'subject' => '🎉 ¡2x1 en Panel LED eléctricos por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Panel LED eléctricos</strong>  y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 3,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉✨

Llévate nuestros Techos LED a un precio especial por ser nuevo cliente.
Este producto mejora la visibilidad de tu negocio y atrae más clientes. 
No te quedes sin el tuyo. 😱🔥
Responde “ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 3,
                'subject' => '🔥Renueva tu espacio con 10% OFF en Techos LED ✨ ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Los Techos LED no solo decoran, también ayudan a transformar cualquier espacio en un ambiente más moderno, profesional y visualmente atractivo para tus clientes. </p>
<p>Además, son personalizables en:</p>
<p>🎨 Color   |   ✏️ Diseño   |  📏 Tamaño</p>
<p>Ideales para tiendas, oficinas, vitrinas, restaurantes, recepciones y negocios que buscan destacar visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un 10% OFF en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 3,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel? 🚀✨

La gran ventaja de nuestros Techos LED  es que hacen que tu marca sea más visible y memorable frente a la competencia.
Si cierras la compra hoy te llevas un 15% de descuento.
¿Estás listo para pasar al siguiente nivel? 📈😎
Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 3,
                'subject' => '⏰ Últimas horas para aprovechar el 15% OFF en Techos LED ✨ ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestros Techos LED.</p>
<p>Hoy, muchos negocios utilizan Techos LED para reforzar su imagen, comunicar promociones y crear espacios más atractivos que capten la atención de sus clientes. Un diseño visual impactante siempre genera mayor recordación.🔥</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>
',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 3,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Techos LED y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 3,
                'subject' => '🎉 ¡2x1 en Techos LED por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
                
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.<p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva 2x1 en Techos LED y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 6,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉✨

Llévate nuestros letreros neón LED a un precio especial por ser nuevo cliente.
Recuerda que este producto mejora la visibilidad de tu negocio y atrae más clientes. 
No te quedes sin el tuyo. 😱🔥
Responde “ACTIVAR” para tu descuento.',
            ],
            
            [
                'template_variant_id' => 2,
                'product_id' => 6,
                'subject' => '🚀 Lleva tu negocio al siguiente nivel con 10% OFF en Letreros Neon LED ',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Los Letreros Neón LED no solo decoran, también ayudan a que tu negocio destaque y genere una imagen más moderna y atractiva para tus clientes.</p>
<p>Además, son personalizables en:</p>
    <p>color, diseño y tamaño,</p>
<p>Ideales para locales, eventos, cafeterías, tiendas y espacios que buscan diferenciarse visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un <strong>10% OFF</strong> en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 6,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel? 🚀

La gran ventaja de nuestros paneles Led electrónicos es generar un ambiente envolvente que hace que las personas permanezcan más tiempo en tu espacio.
Si cierras la compra hoy te llevas un 15% de descuento.
¿Estás listo para pasar al siguiente nivel? 📈😎
Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 6,
                'subject' => '⏰ Últimas horas para hacer destacar tu negocio ✨',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestros Letreros Neón LED.</p>
<p>Hoy, muchos negocios utilizan elementos visuales llamativos para atraer más miradas, generar recordación y destacar frente a la competencia. Un espacio que impacta visualmente, se queda en la mente del cliente. ✨</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 6,
                'content' => 'Hola {{nombre}}
🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Paneles LED Electrónicos y paga solo 1. 😱

Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 6,
                'subject' => '🎉 ¡2x1 en Letreros Neon LED por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>
<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Letreros Neon LED</strong> y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 2,
                'content' => 'Hola {{nombre}}
¡Solo por hoy 10% de descuento en tu primera compra! 🎉✨

Llévate nuestros pisos LED a un precio especial por ser nuevo cliente.
Este producto transforma tu espacio en algo innovador que llama la atención de todos. 
No te quedes sin el tuyo. 😱🔥
Responde “ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 2,
                'subject' => '🔥 Impacta a tus clientes con 10% OFF en Pisos LED ✨',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Los Pisos LED no solo decoran, también crean una experiencia visual única que hace que tu negocio se vea más moderno, innovador y llamativo para tus clientes.</p>
<p>Además, son personalizables en:</p>
<p>Color, diseño y tamaño.</p>
<p>Ideales para eventos, discotecas, tiendas, negocios y espacios que buscan diferenciarse visualmente.</p>
<p>Y por ser nuevo cliente, hoy puedes acceder a un 10% OFF en tu proyecto ✨</p>
<p>Dale click al botón “ACTIVAR” para acceder a tu descuento 🤗</p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios ',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 2,
                'content' => 'Hola {{nombre}}
¿Listo para que tu negocio destaque a otro nivel? 🚀✨

La gran ventaja de nuestros pisos LED es que generan una experiencia diferente que hace que los clientes permanezcan más tiempo.
Si cierras la compra hoy te llevas un 15% de descuento.
¿Estás listo para pasar al siguiente nivel? 📈☺️
 Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 2,
                'subject' => '🔥Últimas horas de 15% OFF para impactar con Pisos LED ✨',
                'content' => '<p>Hola, {{nombre}} 😊</p>
<p>Todavía estás a tiempo de aprovechar el 15% OFF en nuestros Pisos LED.</p>
<p>Hoy, muchos negocios utilizan elementos visuales innovadores para sorprender a sus clientes, atraer más atención y diferenciarse visualmente de la competencia. Un espacio moderno y llamativo siempre genera mayor recordación. ✨</p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p>Activa tu descuento dandole click al botón debajo ⬇️</p>
<p>✨¡No te lo puedes perder! 🔥</p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 2,
                'content' => '🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Pisos LED y paga solo 1. 😱
Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 2,
                'subject' => '🎉¡2x1 en Pisos LED por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>

<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Pisos LED</strong> y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2X1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios ',
            ],

            [
                'template_variant_id' => 1,
                'product_id' => 1,
                'content' => 'Hola {{nombre}} 
¡Solo por hoy 10% de descuento en tu primera compra! ✨🎉

Llévate nuestros hologramas 3D a un precio especial por ser nuevo cliente.
Este producto genera alto impacto visual y atrae más miradas hacia tu negocio. 
No te quedes sin el tuyo. 😱🔥
Responde “ACTIVAR” para tu descuento.',
            ],

            [
                'template_variant_id' => 2,
                'product_id' => 1,
                'subject' => '🚀 Lleva tu negocio al siguiente nivel con 10% OFF en hologramas 3D',
                'content' => '<p>Hola {{nombre}} </p>
<p>Sabemos que destacar hoy en día no es fácil, pero con nuestros hologramas 3D puedes lograrlo de manera impactante.</p>
<p>Por tiempo limitado, queremos ofrecerte un <strong>🔥 10% DE DESCUENTO 🔥</strong> para que implementes esta tecnología en tu negocio, con equipos de alta calidad y acompañamiento en la elección ideal según tu espacio. </p>
<p>Imagina tener contenido en movimiento flotando frente a tus clientes, generando curiosidad y aumentando tu alcance visual. </p>
<p><strong>No dejes pasar esta oportunidad de innovar.</strong></p>
<p><strong>Contáctanos y aprovecha este beneficio exclusivo ✨</strong></p>',
                'cta_text' => 'ACTIVAR 10%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios ',
            ],

            [
                'template_variant_id' => 3,
                'product_id' => 1,
                'content' => '¿Listo para que tu negocio destaque a otro nivel? 🚀✨
La gran ventaja de nuestros hologramas 3D es que posiciona tu marca como innovadora y diferente frente a la competencia.
Si cierras la compra hoy te llevas un 15% de descuento.
¿Estás listo para pasar al siguiente nivel? 📈😎
 Responde CONTINUAR.',
            ],

            [
                'template_variant_id' => 4,
                'product_id' => 1,
                'subject' => '⏰ Últimas horas para impactar con Hologramas 3D ✨',
                'content' => '<p>Hola {{nombre}}</p>
<p>Todavía estás a tiempo de aprovechar el <strong>15% OFF</strong> en nuestros Hologramas 3D.</p>
<p>Hoy, muchos negocios utilizan Proyectores Holográficos 3D para captar más atención, generar experiencias visuales sorprendentes y diferenciarse de la competencia con tecnología innovadora. </p>
<p>Un negocio que impacta visualmente, permanece en la mente del cliente. ✨ </p>
<p>Tu descuento especial vence en menos de 24 horas 🚀</p>
<p><strong>Activa tu descuento dandole click al botón debajo ⬇️</strong></p>
<p><strong>✨¡No te lo puedes perder! 🔥</strong></p>',
                'cta_text' => 'ACTIVAR 15%',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

            [
                'template_variant_id' => 5,
                'product_id' => 1,
                'content' => '🔥 ¡Llegó la mejor oferta del mes! 🔥
Lleva 2 Hologramas 3D y paga solo 1. 😱
Duplica el impacto visual de tu negocio, crea ambientes más llamativos y haz que cada persona que pase frente a tu local voltee a mirar.
⏳ Promoción por tiempo limitado.
Responde "QUIERO MI 2X1" y aprovecha esta oportunidad. 🚀',
            ],

            [
                'template_variant_id' => 6,
                'product_id' => 1,
                'subject' => '🎉 ¡2x1 en Hologramas 3D por tiempo limitado! ',
                'content' => '<p>Hola {{nombre}},</p>

<p>Es el momento perfecto para llevar tu negocio al siguiente nivel.</p>
<p>Por ser nuevo cliente, accede a nuestra promoción exclusiva <strong>2x1 en Holograma 3D</strong> y aprovecha una oportunidad única para potenciar tus espacios, eventos o estrategias de comunicación visual. 🚀</p>
<p>✨ Más visibilidad</p>
<p>✨ Más impacto</p>
<p>✨ Más oportunidades para tu negocio</p>
<p>⏰ Promoción válida por 24 horas.</p>
<p><strong>No dejes pasar esta oportunidad y aprovecha este beneficio exclusivo. 🔥</strong></p>
<p><strong>Activa tu promoción haciendo clic en el botón de abajo. 👇</strong></p>
<p><strong>🔥 ¡No te lo puedes perder!</strong></p>',
                'cta_text' => 'ACTIVAR 2x1',
                'cta_url' => 'https://wa.me/51912849782?text=Hola%20Yuntas%2C%20quisiera%20mas%20informacion%20sobre%20sus%20servicios',
            ],

        ];

        foreach ($overrides as $data) {

            $data['subject'] = $data['subject'] ?? null;
            $data['content'] = $data['content'] ?? null;
            $data['cta_text'] = $data['cta_text'] ?? null;
            $data['cta_url'] = $data['cta_url'] ?? null;
            $data['variables'] = $data['variables'] ?? null;
            $data['assets'] = $data['assets'] ?? null;
            $data['active'] = $data['active'] ?? true;

            TemplateVariantProductOverride::updateOrCreate(
                [
                    'template_variant_id' => $data['template_variant_id'],
                    'product_id' => $data['product_id'],
                ],
                $data
            );
        }
    }
}