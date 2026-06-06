<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ProductContentItem;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ─────────────────────────────
        // CATEGORÍAS
        // ─────────────────────────────

        $categories = [

            'Proyectores',

            'Iluminación LED',

            'Señalización y Letreros',

            'Pantallas y Paneles LED',

            'Mobiliario LED',

            'Publicidad Digital',
        ];

        $categoryMap = [];

        foreach ($categories as $name) {

            $category = Category::firstOrCreate(
                [
                    'slug' => Str::slug($name)
                ],
                [
                    'name' => $name,
                    'slug' => Str::slug($name)
                ]
            );

            $categoryMap[$name] = $category;
        }

        // ─────────────────────────────
        // PRODUCTOS
        // ─────────────────────────────

        $products = [

            // PROYECTORES

            [
                'name' => 'Hologramas',
                'price' => 0,
                'categories' => ['Proyectores'],
                'description' => 'Los proyectores holográficos 3D, también conocidos como ventiladores holográficos, proyectan imágenes tridimensionales flotantes que captan la atención desde cualquier ángulo. Son una solución innovadora para publicidad, activaciones de marca y eventos, permitiendo mostrar productos, logotipos y animaciones con un alto impacto visual sin necesidad de pantallas físicas.',
                'hero_title' => 'Proyectores Holográficos 3D para Publicidad Impactante',
                'slug' => 'ventilador-holograficos-3d',
                'meta_title' => 'A1 Hologramas 3D para Publicidad | Yuntas',
                'meta_description' => 'Hologramas 3d en Perú ideales para eventos y publicidad. Crea experiencias visuales impactantes que atraen clientes y posiciona tu marca con innovación.',
                'keywords' => [
                    'hologramas 3D',
                    'ventilador holograma 3D',
                    'hologramas 3D peru',
                    'proyectores holográficos',
                    'proyector 3d publicitario',
                    'publicidad con hologramas',
                    'hologramas 3d para eventos',
                    'display holográfico 3d',
                    'tecnologia para publicidad',
                    'venta hologramas 3d',
                    'tecnologia holograma 3d',
                    'ventilador holografico para publicidad',
                    'proveedor hologramas peru'
                ],
                'specs' => [
                    'Tecnología LED de alta velocidad',
                    'Proyección de imágenes tridimensionales flotantes',
                    'Visualización desde múltiples ángulos',
                    'Funcionamiento continuo para uso comercial'
                ],
                'benefits' => [
                    'Alto impacto visual para atraer clientes',
                    'Diferenciación inmediata de la marca',
                    'No requiere pantallas ni lentes especiales',
                    'Mejora la recordación de marca'
                ]

            ],

            // ILUMINACIÓN LED

            [
                'name' => 'Pisos LED',
                'price' => 0,
                'categories' => ['Iluminación LED'],
                'description' => 'Los pisos LED interactivos son superficies iluminadas con tecnología LED integrada que permiten emitir luz en múltiples colores, patrones y efectos visuales dinámicos. Están fabricados con materiales resistentes como vidrio templado o acrílicos reforzados, diseñados para soportar cargas elevadas y tránsito continuo. Su sistema modular facilita la instalación, el mantenimiento y la personalización del diseño. Además, pueden programarse para mostrar gráficos, animaciones o responder al movimiento, creando ambientes llamativos e inmersivos ideales para eventos, escenarios y espacios de alto impacto visual.',
                'hero_title' => 'Pisos LED interactivos y personalizables para eventos impactantes',
                'slug' => 'pistas-de-baile-led',
                'meta_title' => 'Pisos LED para Eventos y Fiestas | Perú',
                'meta_description' => 'Pisos LED interactivos en Perú ideales para eventos, fiestas y activaciones. Atrae miradas y crea experiencias impactantes con soluciones modernas, resistentes.',
                'keywords' => [
                    'pisos led',
                    'pistas de baile led',
                    'pisos led para fiestas',
                    'pisos led Perú',
                    'pisos led precio',
                    'pista led para baile',
                    'pista de baile led peru',
                    'pistas led para eventos',
                    'pisos led para eventos corporativos',
                    'pista led para discotecas',
                    'pisos led interactivos',
                    'pisos led sin fondos',
                    'pisos led para fiestas precio'
                ],
                'specs' => [
                    'Funcionan a través de sensores de movimiento',
                    'Software que recepciona y ejecuta comandos',
                    'Tecnología LED integrada',
                    'Superficie resistente para alto tránsito',
                    'Paneles modulares de fácil instalación',
                    'Opción de personalización de efectos',
                    'Uso en eventos y fiestas'
                ],
                'benefits' => [
                    'Crean experiencias interactivas memorables',
                    'Aumentan el impacto visual del evento',
                    'Permiten personalización de colores y efectos',
                    'Transforman espacios comunes en escenarios llamativos',
                    'Atraen la atención del público de inmediato',
                    'Refuerzan la identidad y diferenciación del evento'
                ]
            ],

            [
                'name' => 'Techos Led',
                'price' => 0,
                'categories' => ['Iluminación LED'],
                'description' => 'Los Techos LED son estructuras iluminadas que incorporan paneles de luz LED como parte del diseño del techo, brindando una iluminación uniforme, eficiente y visualmente impactante. Cuentan con iluminadores LED que permiten ajustar la intensidad y el color, con opciones en un solo tono o multicolor RGB, todo controlado mediante un sistema de control.
Estos techos pueden personalizarse en tamaño, forma y configuración según el espacio, e incluso incluir funciones avanzadas como cambio de colores, regulación de intensidad o simulación de luz natural. Están fabricados con materiales ligeros y resistentes, lo que facilita su instalación y asegura una larga vida útil.
Son ideales para espacios que buscan una combinación de funcionalidad y estética moderna, como carwash, gimnasios, discotecas, centros de mantenimiento de autos de lujo y establecimientos de ropa deportiva.',
                'hero_title' => 'Techos LED modernos y personalizables para iluminación de alto impacto',
                'slug' => 'techos-led-comerciales',
                'meta_title' => 'Techos LED decorativos │ Perú',
                'meta_description' => 'Techos LED con iluminación RGB personalizable, control de intensidad y diseño moderno. Ideales para espacios comerciales y de alto impacto visual.',
                'keywords' => [
                    'techos led',
                    'iluminación rgb',
                    'foseado led',
                    'iluminación led indirecta',
                    'techo moderno led',
                    'iluminación led personalizable',
                    'techo led interior',
                    'techos modernos con led',
                    'iluminación led para techos',
                    'techos de lujo led',
                    'Techo hexagonal led',
                    'Luces de techo led',
                    'Techos con luz LED'
                ],
                'specs' => [
                    'Iluminadores LED con control de intensidad',
                    'Opción de iluminación en un solo color o RGB',
                    'Sistema de control para monitoreo y ajustes',
                    'Personalización en tamaño y forma',
                    'Iluminación uniforme y eficiente',
                    'Diseño integrado al techo',
                    'Materiales ligeros y resistentes',
                    'Uso en espacios comerciales y modernos'
                ],
                'benefits' => [
                    'Mejoran la estética del espacio',
                    'Permiten crear ambientes modernos y sofisticados',
                    'Ofrecen control total de la iluminación',
                    'Se adaptan a diferentes tipos de negocios',
                    'Aumentan la percepción de lujo y orden',
                    'Optimiza la iluminación sin recargar el diseño'
                ]
            ],

            [
                'name' => 'Barras Pixel LED',
                'price' => 0,
                'categories' => ['Iluminación LED'],
                'description' => 'Las Barras Pixel LED son sistemas de iluminación lineal compuestos por LEDs direccionables individualmente, que permiten crear efectos visuales dinámicos como animaciones, patrones de movimiento y cambios de color sincronizados. Se fabrican a medida según el diseño del espacio y pueden instalarse en techos, muros o estructuras colgantes. Son ideales para locales nocturnos, eventos y proyectos arquitectónicos que buscan un alto impacto visual y una experiencia envolvente.',
                'hero_title' => 'Barras Pixel LED para Iluminación Dinámica y Escenográfica',
                'slug' => 'iluminacion-pixel-led',
                'meta_title' => 'Barras Pixel LED │ Eventos y Locales Nocturnos',
                'meta_description' => 'Barras Pixel LED personalizables con efectos dinámicos. Ideales para discotecas, eventos, karaokes y decoración de techos.',
                'keywords' => [
                    'techos led',
                    'barras pixel led',
                    'barras led rgb',
                    'barras led interactivas',
                    'barras led para eventos',
                    'barras led para discotecas',
                    'iluminación pixel led',
                    'decoración led',
                    'led pixel exteriores',
                    'led pixel interiores',
                    'led pixel decorativo',
                    'led pixel para fachada',
                    'decoración led pixel',
                    'iluminación led pixel',
                    'tecnología led pixel',
                    'efectos led pixel',
                    'animaciones con led pixel',
                    'venta de led pixel',
                ],
                'specs' => [
                    'Medidas personalizadas según proyecto',
                    'Efectos visuales programables',
                    'Cambios de color RGB dinámicos',
                    'Uso interior y exterior',
                    'Materiales resistentes y compactos',
                ],
                'benefits' => [
                    'Alto impacto visual en espacios nocturnos',
                    'Transforma ambientes con iluminación dinámica',
                    'Total personalización de efectos y colores',
                    'Ideal para discotecas y locales de entretenimiento',
                    'Solución moderna y llamativa para decoración',
                ]
            ],

            [
                'name' => 'Neón LED',
                'price' => 0,
                'categories' => ['Iluminación LED'],
                'description' => 'Letras luminosas con tecnología LED que simulan el aspecto del neón tradicional, ofreciendo una alternativa moderna, eficiente y duradera. El neón LED brinda una iluminación brillante con bajo consumo energético, ideal para interiores y negocios. Permite personalizar colores y estilos, adaptándose a distintos espacios comerciales y creativos, con alta durabilidad y un funcionamiento económico.',
                'hero_title' => 'Letras de Neón LED Personalizadas',
                'slug' => 'neon-led-personalizado',
                'meta_title' => 'Neón LED para Interiores │ Perú',
                'meta_description' => 'Neón LED personalizado para negocios, eventos y decoración. Diseños modernos, bajo consumo y máxima visibilidad para destacar tu marca.',
                'keywords' => [
                    'neón led',
                    'letreros neón led',
                    'neón led personalizadas',
                    'neón led Perú',
                    'letreros luminosos neón',
                    'decoracion led',
                    'decoracion neon led eventos',
                    'neon led para eventos',
                    'neon led para espacios nocturnos',
                ],
                'specs' => [
                    'Tecnología LED de bajo consumo energético',
                    'Alta durabilidad',
                    'Iluminación brillante tipo neón',
                ],
                'benefits' => [
                    'Alternativa moderna al neón tradicional',
                    'Personalizable en colores y estilos',
                    'Ideal para interiores y espacios comerciales',
                ]
            ],

            [
                'name' => 'Letreros Neón LEDs',
                'price' => 0,
                'categories' => ['Iluminación LED'],
                'description' => 'Dispositivos luminosos diseñados para emitir luz brillante y colorida mediante tecnología LED, que simulan el aspecto del neón tradicional. Los letreros neón LED están compuestos por tubos flexibles de material acrílico o silicona con luces LED integradas, ofreciendo una alternativa moderna, duradera y económica frente al neón de vidrio. Son ligeros, de fácil instalación y permiten diseños personalizados, adaptándose a aplicaciones decorativas e informativas en interiores y exteriores.',
                'hero_title' => 'Letreros Neón LED Personalizados',
                'slug' => 'letreros-neon-led',
                'meta_title' => 'Letrero Neón LED Personalizables | Perú',
                'meta_description' => 'Letreros neón LED personalizados que captan miradas y hacen destacar tu negocio. Diseños modernos, duraderos y de alto impacto visual.',
                'keywords' => [
                    'letreros neón led',
                    'letreros de neón led perú',
                    'neon led personalizado',
                    'letras de neón led para interiores',
                    'letras de neón led para negocios',
                    'letreros luminosos neon',
                    'letreros neon led precio',
                    'comprar letreros neon led',
                    'cotizar neon led',
                    'letrero neon led para eventos',
                    'letreros neon personalizados',
                    'letreros publicitarios neon led',
                    'letreros neon led para locales nocturnos'
                ],
                'specs' => [
                    'Tecnología LED con tubos flexibles acrílicos o de silicona',
                    'Diseño ligero y de fácil instalación',
                    'Disponibles en diferentes medidas',
                ],
                'benefits' => [
                    'Alternativa duradera y económica al neón de vidrio',
                    'Permite diseños personalizados y versátiles',
                    'Iluminación brillante y eficiente energéticamente',
                ]
            ],

            // SEÑALIZACIÓN Y LETREROS

            [
                'name' => 'Letreros Acrílicos',
                'price' => 0,
                'categories' => ['Señalización y Letreros'],
                'description' => 'Los Letreros Acrílicos son una opción moderna y elegante para señalización y decoración de espacios comerciales y eventos. Están fabricados en acrílico de alta calidad, lo que permite crear diseños volumétricos y en 3D, aportando profundidad y presencia visual a la marca.
Ofrecen una amplia versatilidad de personalización en tamaños, colores y estilos, con la posibilidad de incorporar iluminación para lograr un mayor impacto visual. Su material es duradero, resistente a impactos y de fácil mantenimiento, lo que los hace aptos tanto para interiores como exteriores.
Son ideales para negocios y organizaciones que buscan destacar su identidad visual de manera profesional y atractiva.',
                'hero_title' => 'Letreros acrílicos personalizados y modernos para tu marca',
                'slug' => 'letreros-acrilicos-comerciales',
                'meta_title' => 'Letreros Acrilicos Comercials │ Perú',
                'meta_description' => 'Letreros acrílicos en Perú que hacen destacar tu negocio con diseños personalizados, luz LED y entrega rápida. Cotiza gratis hoy.',
                'keywords' => [
                    'Letreros corporativos',
                    'letreros acrilicos perú',
                    'letreros acrilicos luminosos',
                    'letreros acrílicos personalizados',
                    'letreros luminosos',
                    'letreros acrílicos',
                    'letreros acrílicos baratos',
                    'letreros comerciales',
                    'letreros en acrílico comerciales',
                ],
                'specs' => [
                    'Fabricados en acrílico de alta calidad',
                    'Diseños volumétricos y 3D',
                    'Personalización en tamaño, color y estilo',
                    'Opciones de iluminación',
                    'Resistentes a impactos',
                    'Fácil mantenimiento',
                    'Aptos para interiores y exteriores'
                ],
                'benefits' => [
                    'Aportan una imagen moderna y profesional',
                    'Refuerzan la identidad visual de la marca',
                    'Ofrecen gran durabilidad y resistencia',
                    'Se adaptan a diferentes tipos de espacios',
                    'Generan mayor impacto visual',
                    'Requieren poco mantenimiento'
                ]
            ],

            [
                'name' => 'Letras pintadas en MDF',
                'price' => 0,
                'categories' => ['Señalización y Letreros'],
                'description' => 'Las Letras pintadas en MDF son una solución ideal para decoración y señalización que requieren un alto nivel de personalización y una apariencia elegante. Están fabricadas en MDF (Medium Density Fiberboard), un material versátil que permite crear letras y formas en distintos tamaños, estilos y colores.
Cuentan con acabados premium de pintura que logran una superficie uniforme e impecable, aportando una imagen limpia y profesional. Su diseño facilita una instalación sencilla y las hace adaptables a diversos entornos, especialmente en espacios interiores y exhibiciones temporales.
Son una excelente opción para proyectos decorativos que buscan impacto visual con un acabado cuidado y personalizado.',
                'hero_title' => 'Letras pintadas en MDF personalizadas con acabado premium',
                'slug' => 'letras-en-mdf-comerciales',
                'meta_title' => 'Letras en MDF para Negocios | Yuntas',
                'meta_description' => 'Letras y letreros en MDF  personalizados para negocios. Mejora la imagen de tu empresa con diseños modernos, resistentes y de alta calidad que atraen cli',
                'keywords' => [
                    'letras pintadas en MDF',
                    'letras en MDF',
                    'letras en MDF pintadas',
                    'pintado 3D MDF',
                    'letras MDF personalizadas',
                    'letreros en mdf en lima',
                    'letras en mdf en lima',
                    'letreros mdf personalizados',
                    'letras mdf personalizadas',
                    'letras en mdf comerciales',
                    'letras comerciales',
                    'letras mdf corporeas'
                ],
                'specs' => [
                    'Fabricadas en MDF (Medium Density Fiberboard)',
                    'Alta personalización en formas y tamaños',
                    'Amplia variedad de colores',
                    'Acabados de pintura premium',
                    'Instalación sencilla',
                    'Diseño adaptable a diferentes espacios',
                    'Uso principal en interiores'
                ],
                'benefits' => [
                    'Aportan una imagen elegante y profesional',
                    'Permiten personalización total del diseño',
                    'Ofrecen acabados visualmente impecables',
                    'Se adaptan a distintos estilos decorativos',
                    'Fáciles de instalar',
                    'Ideales para proyectos temporales o decorativos'
                ]
            ],
            

            [
                'name' => 'Menú Board',
                'price' => 0,
                'categories' => ['Señalización y Letreros'],
                'description' => 'Los Menú Boards son sistemas de visualización diseñados para presentar menús de forma clara, atractiva y profesional. Incorporan imágenes en alta definición, gran colorido y variedad de contenidos que capturan la atención del público y mejoran la experiencia del cliente. Están fabricados con materiales resistentes que garantizan durabilidad y un rendimiento óptimo frente a condiciones adversas como humedad o cambios de temperatura. Ideales para restaurantes y locales de comida rápida que buscan comunicar su oferta de manera impactante y eficiente.',
                'hero_title' => 'Menú Boards de Alto Impacto Visual',
                'slug' => 'menu-board-digital',
                'meta_title' => 'Menu Board Digitales │ Yuntas Perú',
                'meta_description' => 'Fabricamos Letreros Board Menu Luminosos, totalmente personalizado para todo tipo de restaurantes en acrilicos luminosos para todo tipo de negocio',
                'keywords' => [
                    'Menú Boards',
                    'Menú Digital',
                    'Restaurantes menú boards',
                    'Letrero menú LED',
                    'Menu board digitales',
                ],
                'specs' => [
                    'Alta definición de imagen',
                    'Resistente a condiciones adversas',
                    'Diseño visual impactante',
                ],
                'benefits' => [
                    'Capta la atención del público',
                    'Mejora la comunicación del menú',
                    'Ideal para restaurantes y fast food',
                ]
            ],

            [
                'name' => 'Letras Doradas y Plateadas',
                'price' => 0,
                'categories' => ['Señalización y Letreros'],
                'description' => 'Las letras doradas y plateadas son una opción lujosa para espacios que buscan resaltar con estilo y sofisticación. Están fabricadas con acabados metálicos brillantes que reflejan la luz y generan un efecto visual elegante e impactante. Su diseño permite elevar la presencia de marcas, espacios comerciales y señalización de alta gama, aportando una imagen profesional y moderna. Son personalizables según las necesidades del proyecto, adaptándose a distintos entornos y usos.',
                'hero_title' => 'Letras metálicas doradas y plateadas de alto impacto visual',
                'slug' => 'letras-metalicas-corporativas',
                'meta_title' => 'Letreros Dorados y Plateados | Yuntás Perú',
                'meta_description' => 'Fabricamos letreros dorados y plateados en Lima. Soluciones personalizadas para negocios que quieren destacar y atraer más clientes.',
                'keywords' => [
                    'Letras Personalizadas Plateadas',
                    'letreros dorados en lima',
                    'letreros plateados en lima',
                    'letreros dorados y plateados en lima',
                    'letras doradas para negocio',
                    'letras doradas precio peru',
                    'letreros para negocio precio',
                    'letras de acero inoxidable',
                    'letreros corporativos',
                    'letras comerciales inoxidables'
                ],
                'specs' => [
                    'Acabados metálicos dorados o plateados',
                    'Superficie brillante con alta capacidad de reflexión',
                    'Diseño personalizable según el espacio',
                ],
                'benefits' => [
                    'Aporta lujo y elegancia al ambiente',
                    'Genera alto impacto visual en la señalización',
                    'Refuerza la imagen profesional de la marca',
                ]
            ],

            [
                'name' => 'Impresión en Vinilo',
                'price' => 0,
                'categories' => ['Señalización y Letreros'],
                'description' => 'La Impresión en Vinilo es una solución versátil y duradera para comunicar mensajes visuales de alto impacto en espacios interiores y exteriores. Permite reproducir gráficos, imágenes y textos con gran nitidez y fidelidad de color, adaptándose a múltiples superficies como vidrios, muros, paneles y vitrinas. Utilizamos materiales resistentes y tintas de alta calidad que aseguran una excelente adherencia y larga duración frente a la exposición solar y condiciones ambientales. Es ideal para branding, señalización, promociones y decoración comercial.',
                'hero_title' => 'Impresión en Vinilo de Alta Calidad',
                'slug' => 'vinilos-publicitarios',
                'meta_title' => 'Impresión de Vinil Publicitario | Yuntas Perú',
                'meta_description' => 'Impresión en vinilo adhesivo para publicidad, decoración y señalización. Alta calidad, resistencia y acabados profesionales para tu marca.',
                'keywords' => [
                    'Impresión en vinilo',
                    'Vinilo adhesivo',
                    'Vinilos publicitarios',
                    'Vinilo para negocios'
                ],
                'specs' => [
                    'Alta calidad de impresión',
                    'Material vinílico resistente',
                    'Aplicación en diversas superficies',
                ],
                'benefits' => [
                    'Comunicación visual llamativa',
                    'Gran durabilidad y adherencia',
                    'Ideal para branding y promociones',
                ]
            ],

            [
                'name' => 'Letreros Luminosos',
                'price' => 0,
                'categories' => ['Señalización y Letreros'],
                'description' => 'Los letreros luminosos son una solución moderna y efectiva para destacar tu negocio tanto de día como de noche. Combinan diseño atractivo con iluminación LED para crear un impacto visual inmediato, mejorando la visibilidad y el reconocimiento de tu marca. Son personalizables en formas, colores y tamaños, lo que permite adaptarlos a la identidad de cualquier negocio. Además, ofrecen durabilidad y bajo consumo energético, convirtiéndose en una inversión inteligente para atraer más clientes y fortalecer tu presencia comercial.',
                'hero_title' => 'Letreros Luminosos que impactan',
                'slug' => 'letrero-luminosos-comerciales',
                'meta_title' => 'Letreros Luminosos Comerciales │ Perú',
                'meta_description' => 'Letreros luminosos para negocios en Perú de calidad y diseño personalizados con precios accesibles, ideales para destacar tu marca.',
                'keywords' => [
                    'letreros luminosos',
                    'letreros luminosos LED',
                    'letreros luminosos personalizados',
                    'letreros LED para negocios',
                    'avisos luminosos'
                ],
                'specs' => [
                    'Iluminación LED de alta intensidad y bajo consumo',
                    'Diseño 100% personalizado (colores, formas y tipografía)',
                    'Materiales resistentes y de larga durabilidad',
                    'Visibilidad garantizada de día y noche'
                ],
                'benefits' => [
                    'Aumenta la visibilidad de tu negocio de inmediato',
                    'Atrae más clientes, incluso en horarios nocturnos',
                    'Refuerza la identidad y reconocimiento de tu marca',
                    'Genera un impacto visual profesional y moderno',
                    'Diferencia tu negocio frente a la competencia'
                ]
            ],

            // PANTALLAS Y PANELES LED

            [
                'name' => 'Paneles LED Electrónicos',
                'price' => 0,
                'categories' => ['Pantallas y Paneles LED'],
                'description' => 'Sirve para colocar textos, videos y publicidad. Puden ser de un solo color o varios. Funciona a 220 y es personalizable.',
                'hero_title' => 'Paneles LED electrónicos para publicidad y ambientación',
                'slug' => 'paneles-led-electronicos',
                'meta_title' => 'Panel de Luz LED para Publicidad | Perú',
                'meta_description' => 'Paneles LED para publicidad que captan miradas y potencia tu marca. Soluciones modernas, personalizables y de alto impacto para negocios.',
                'keywords' => [
                    'paneles led',
                    'panel led publicitario',
                    'panel de luz led',
                    'paneles led Perú',
                    'panel led para negocios',
                    'panel led para publicidad',
                    'panel led percio perú',
                    'comprar panel led',
                    'panel led personalizado',
                    'proveedor panel led perú',
                    'panel led para fachada negocio',
                    'panel de luz led publicitario',
                    'panel led para tienda'
                ],
                'specs' => [
                    'Tecnología LED para visualización digital',
                    'Funcionamiento a 220 V',
                    'Personalizables en color, formato y tamaño',
                ],
                'benefits' => [
                    'Alta visibilidad para publicidad y comunicación',
                    'Adaptable a diferentes tipos de negocios',
                    'Uso versátil en interiores y eventos',
                ]
            ],

            [
                'name' => 'Pantallas LED',
                'price' => 10,
                'categories' => ['Pantallas y Paneles LED'],
                'description' => 'Las pantallas LED son dispositivos de visualización electrónica diseñados para proyectar imágenes, videos y textos con alto brillo, colores vibrantes y excelente resolución. Están compuestas por paneles modulares armables que permiten crear pantallas a medida, a partir de metro y medio, adaptándose a diferentes tamaños y configuraciones. Funcionan mediante un software especializado que garantiza una óptima calidad de imagen y permiten su uso tanto en interiores como en exteriores. Son ideales para publicidad, eventos, escenarios y señalización digital, destacando por su versatilidad, durabilidad y capacidad de mostrar contenido dinámico en tiempo real.',
                'hero_title' => 'Pantallas LED de alta resolución para interiores y exteriores',
                'slug' => 'pantallas-led-publicitarias',
                'meta_title' => 'Pantallas LED publicitarias │ Yuntas Perú',
                'meta_description' => 'Pantallas LED modulares y personalizables para interiores y exteriores. Ideales para publicidad, eventos y escenarios con alta calidad de imagen.',
                'keywords' => [
                    'pantallas led',
                    'pantallas led rgb',
                    'pantallas led publicitarias',
                    'pantallas led para eventos',
                    'pantallas led armables',
                    'pantalla led escenario',
                    'pantalla led interior',
                    'pantalla led exterior',
                    'pantallas led de alta resolución',
                    'pantallas led a medida',
                    'Pantallas led para eventos',
                    'Pantallas led para centros comerciales',
                    'Pantallas led Peru'
                ],
                'specs' => [
                    'Pantallas modulares armables desde metro y medio',
                    'Uso en interiores y exteriores',
                    'Software de control para alta resolución de imagen',
                ],
                'benefits' => [
                    'Alta calidad de imagen y brillo',
                    'Adaptables a distintos tamaños y configuraciones',
                    'Ideales para publicidad, eventos y lanzamientos de marca',
                ]
            ],



            // MOBILIARIO LED

            [
                'name' => 'Mesas y sillas LED',
                'price' => 0,
                'categories' => ['Mobiliario LED'],
                'description' => 'Mobiliario LED innovador que integra iluminación full color en mesas y sillas de diseño moderno. Fabricados con materiales resistentes y translúcidos, estos muebles permiten una difusión uniforme de la luz y pueden personalizarse con el logo del cliente. Incorporan efectos lumínicos fijos o cambiantes, controlables mediante sistemas sencillos, aportando un estilo sofisticado y futurista a cualquier espacio. Ideales para eventos, terrazas, discotecas y ambientes modernos que buscan destacar con una decoración luminosa y funcional.',
                'hero_title' => 'Pantallas LED de alta resolución para interiores y exteriores',
                'slug' => 'muebles-led-iluminados',
                'meta_title' => 'Mesas y Sillas LED │ Perú',
                'meta_description' => 'Mesas y sillas LED con iluminación full color y diseño moderno. Mobiliario luminoso ideal para eventos, discotecas y espacios sofisticados.',
                'keywords' => [
                    'sillas luminosas',
                    'mobiliario iluminado led',
                    'precios de sillas led',
                    'mesa centro led',
                    'sillas con luces led',
                    'sillas decorativas luminosas',
                    'comprar sillas luminosas',
                    'precio de sillas led',
                    'asientos luminosos led',
                    'decoración con luz',
                    'mobiliario iluminado',
                    'diseño de eventos',
                    'sillas led para eventos'
                ],
                'specs' => [
                    'Iluminación LED full color integrada',
                    'Material translúcido de alta resistencia',
                    'Efectos lumínicos fijos o cambiantes',
                ],
                'benefits' => [
                    'Ideal para eventos y espacios nocturnos',
                    'Personalizable con logo del cliente',
                    'Crea ambientes modernos y sofisticados',
                ]
            ],

            // PUBLICIDAD DIGITAL

            [
                'name' => 'Monitores de Publicidad Digital',
                'price' => 0,
                'categories' => ['Publicidad Digital'],
                'description' => 'Los Monitores de Publicidad Digital son soluciones visuales diseñadas para comunicar mensajes de manera dinámica, moderna y altamente efectiva. Permiten mostrar contenido multimedia en alta resolución, como imágenes, videos y promociones, captando la atención del público en puntos estratégicos. Están fabricados con tecnología de alto rendimiento y materiales resistentes, ideales para un funcionamiento continuo en entornos comerciales. Son perfectos para tiendas, restaurantes, centros comerciales y espacios corporativos que buscan una comunicación visual clara, atractiva y actualizable en tiempo real.',
                'hero_title' => 'Monitores de Publicidad Digital de Alto Impacto',
                'slug' => 'pantallas-publicitarias-digitales',
                'meta_title' => 'Monitores Digitales Publicitarios │ Perú',
                'meta_description' => 'Monitores de publicidad digital para mostrar contenido dinámico en tiendas, restaurantes y empresas. Aumenta visibilidad y capta clientes.',
                'keywords' => [
                    'Monitores de publicidad digital',
                    'Pantallas publicitarias digitales',
                    'Pantalla LED publicidad',
                    'Monitores comerciales',
                    'pantallas publicitarias',
                    'pantallas led para publicidad',
                    'señalización digital',
                    'monitores para tiendas',
                    'publicidad visual digital',
                    'monitores para retail'
                ],
                'specs' => [
                    'Alta resolución de imagen',
                    'Funcionamiento continuo 24/7',
                    'Compatible con contenido multimedia',
                ],
                'benefits' => [
                    'Aumenta la visibilidad de promociones',
                    'Comunicación dinámica y actualizable',
                    'Mejora la experiencia del cliente', 
                ]
            ],
        ];

        foreach ($products as $data) {

            $product = Product::firstOrCreate(
                [
                    'slug' => $data['slug'] ?? Str::slug($data['name'])
                ],
                [
                    'name' => $data['name'],

                    'slug' => $data['slug'] ?? Str::slug($data['name']),

                    'hero_title' => $data['hero_title'] ?? $data['name'],

                    'description' => $data['description'] ?? "Solución profesional de {$data['name']}",

                    'price' => $data['price'],

                    'status' => 'active',

                    'meta_title' => $data['meta_title'] ?? $data['name'],

                    'meta_description' => $data['meta_description'] ?? $data['name'],

                    'keywords' => $data['keywords'] ?? [$data['name']]
                ]
            );

            ProductContentItem::where('product_id', $product->id)->delete();

            if (isset($data['specs'])) {

                foreach ($data['specs'] as $spec) {

                    ProductContentItem::create([
                        'product_id' => $product->id,
                        'slot_id' => 1,
                        'text' => $spec
                    ]);
                }
            }

            if (isset($data['benefits'])) {

                foreach ($data['benefits'] as $benefit) {

                    ProductContentItem::create([
                        'product_id' => $product->id,
                        'slot_id' => 2,
                        'text' => $benefit
                    ]);
                }
            }

            $categoryIds = collect($data['categories'])
                ->map(fn($name) => $categoryMap[$name]->id)
                ->values()
                ->toArray();

            $product->categories()
                ->syncWithoutDetaching($categoryIds);
        }

    }
}
