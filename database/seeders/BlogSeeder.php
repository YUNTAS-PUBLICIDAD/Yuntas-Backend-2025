<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Blog;
use App\Models\BlogContentItem;
use App\Models\BlogContentText;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $blogs = [

            [
                'product_id' => 1,
                'title' => 'Cómo atraer clientes con Proyectores Holográficos 3D en 2026',
                'slug' => 'guia-proyectores-holograficos-3d-ventas',
                'hero_title' => 'El futuro de la publicidad visual aquí',
                'cover_subtitle' => 'Guía definitiva para transformar tu vitrina en una experiencia visual futurista.',
                'meta_title' => 'Decoración con Neón LED para Negocios: Guía y Consejos',
                'meta_description' => 'Descubre cómo el Neón LED puede transformar tu local. Aprende sobre diseños personalizados, bajo consumo y cómo atraer más clientes con luz.',
                'description' => 'Los [custom:https://yuntaspublicidad.com/productos/ventilador-holograficos-3d/|proyectores holográficos han revolucionado] la forma en que las marcas interactúan con su público. En este artículo exploramos su configuración y beneficios.',
                'benefits' => [
                    'Impacto visual inmediato.',
                    'Bajo consumo energético.',
                    'Fácil actualización de contenido.'
                ],
                'testimonial' => '"Desde que instalamos el proyector de Yuntas, el tráfico en nuestra tienda aumentó un 40%." - Camil Velázquez, Gerenta de Marketing.'

            ],

            [
                'product_id' => 9,
                'title' => 'Cómo cuidar un menu board digital y evitar fallas en pantalla',
                'slug' => 'como-cuidar-menu-board-digital',
                'hero_title' => 'Cómo cuidar un menu board digital y evitar fallas en pantalla',
                'cover_subtitle' => 'Claves prácticas para mantener tu menu board en buen estado y asegurar una comunicación visual clara',
                'meta_title' => 'Cómo cuidar un menu board digital correctamente',
                'meta_description' => 'Conoce cómo cuidar un menu board digital, evitar fallas en pantalla y mantener una comunicación visual clara en tu negocio.',
                'description' => 'En muchos[product:menu-board|restaurantes y locales de comida rápida, el menu board]es el primer contacto visual entre el negocio y el cliente. Más que una pantalla, se trata de una herramienta de comunicación que organiza la información, guía la decisión de compra y marca el ritmo del servicio. Por eso, su cuidado influye directamente en la experiencia que recibe el público.
Factores como la ubicación, la limpieza y el manejo del contenido suelen pasar desapercibidos en el día a día. El calor excesivo, el vapor de la cocina o la exposición constante a la luz pueden afectar el rendimiento de la pantalla. A ello se suma el uso de materiales inadecuados para la limpieza, que con el tiempo reducen la nitidez de la imagen y dificultan la lectura del menú.
Asimismo, el contenido que se proyecta cumple un rol clave. Archivos pesados o mal optimizados pueden generar fallas, interrupciones o retrasos en la reproducción. Un menu board que funciona correctamente no solo depende de la tecnología instalada, sino del cuidado continuo que se le brinda como parte del funcionamiento diario del local.',
                'benefits' => [
                    'Mantiene la pantalla en buen estado por más tiempo',
                    'Mejora la legibilidad del menú para los clientes',
                    'Reduce fallas técnicas y cortes en la visualización'
                ],
                'testimonial' => '"Desde que cuidamos mejor nuestro menu board digital, la información se ve más clara y el servicio es más ordenado. La pantalla se mantiene estable y sin interrupciones durante el día.”-Ximena Taboada'
            ],

            [
                'product_id' => 8,
                'title' => '¿Cómo mantengo en buen estado mis letras pintadas en MDF?',
                'slug' => 'letras-pintadas-mdf-cuidado-durabilidad',
                'hero_title' => 'Letras pintadas en MDF: diseño que se cuida en cada detalle',
                'cover_subtitle' => 'Descubre cómo conservar letras pintadas en MDF y mantener su acabado en perfecto estado',
                'meta_title' => 'Letras pintadas en MDF: cuidado y durabilidad',
                'meta_description' => 'Conoce cómo cuidar letras pintadas en MDF para mantener su diseño, acabado y durabilidad en espacios interiores.',
                'description' => 'Las letras pintadas en MDF son una opción decorativa y funcional muy utilizada en señalética, activaciones de marca, vitrinas, eventos y espacios corporativos. Su acabado limpio y la posibilidad de personalización en tamaños, tipografías y colores las convierten en un recurso versátil y atractivo. Sin embargo, para conservar su apariencia y durabilidad, es importante aplicar ciertos cuidados básicos.
Uno de los principales aspectos a considerar es la protección frente a la humedad. El MDF es un material sensible al agua, por lo que la exposición prolongada a ambientes húmedos puede generar hinchazón o deformaciones. Por ello, se recomienda utilizar estas letras en interiores o en espacios controlados.
El mantenimiento del acabado también es clave. La pintura puede deteriorarse con el roce constante, golpes o una limpieza inadecuada. Para preservar el color y la textura, es importante evitar productos abrasivos y manipulaciones bruscas durante su instalación o traslado.
Asimismo, la instalación correcta influye directamente en su conservación. Un soporte mal fijado o una superficie irregular puede provocar desprendimientos o quiebres. Utilizar sistemas de anclaje adecuados ayuda a mantener las letras en buen estado y alineadas.
Finalmente, el almacenamiento cumple un rol importante cuando las letras no están en uso. Guardarlas en lugares secos, protegidas del polvo y separadas entre sí, evita daños en los bordes y en la pintura.',
                'benefits' => [
                    'Limpia las letras con un paño seco o ligeramente húmedo.',
                    'Evita colocarlas cerca de fuentes directas de calor o humedad.',
                    'Manipúlalas con cuidado durante el montaje y desmontaje.'
                ],
                'testimonial' => '"Gracias, Yuntas. Elegimos letras pintadas en MDF para nuestro espacio porque buscábamos una solución estética y funcional"-Valentina Castillo'
            ],

            [
                'product_id' => 1,
                'title' => 'Proyectores holográficos 3D: cómo funcionan y dónde se utilizan',
                'slug' => 'proyectores-holograficos-3d',
                'hero_title' => 'Proyectores holograficos 3d',
                'cover_subtitle' => 'Proyectores holograficos 3d',
                'meta_title' => 'Proyectores holográficos 3D: qué son y cómo funcionan',
                'meta_description' => 'Descubre qué son los proyectores holográficos 3D, cómo funcionan y en qué sectores se utilizan para crear proyecciones visuales innovadoras.',
                'description' => 'Los hologramas 3D son una tecnología visual que permite mostrar imágenes con apariencia tridimensional, creando la sensación de que los objetos flotan en el aire. Esta proyección se realiza a través de proyectores holográficos 3D, dispositivos que combinan iluminación LED, movimiento y software especializado para generar el efecto visual sin necesidad de pantallas tradicionales.
Actualmente, los hologramas 3D se utilizan en distintos sectores como la publicidad, los eventos, el comercio y las exhibiciones tecnológicas. Su principal característica es la capacidad de mostrar contenido dinámico desde varios ángulos, lo que los convierte en una herramienta llamativa para transmitir información, promocionar productos o reforzar la identidad visual de una marca.
El funcionamiento de estos proyectores se basa en la proyección de imágenes sincronizadas sobre superficies en movimiento o sistemas ópticos que simulan profundidad. Dependiendo del modelo, pueden reproducir animaciones, logotipos, textos o figuras en rotación, logrando una experiencia visual innovadora y moderna.',
                'benefits' => [
                    'Colocar el proyector en un punto estratégico con buena visibilidad.',
                    'Evitar la iluminación directa que pueda afectar la proyección.',
                    'Utilizar contenido diseñado específicamente para formato holográfico.'
                ],
                'testimonial' => 'Cuando vimos un holograma 3D por primera vez, fue imposible no detenernos a mirar. La imagen parecía flotar y eso hizo que el mensaje se sintiera diferente, más cercano y fácil de recordar.-Cindy Vasquéz'

            ],

            [
                'product_id' => 2,
                'title' => '¿Cómo cuidar tu piso LED para que dure más tiempo y mantenga su calidad?',
                'slug' => 'como-cuidar-tu-piso-led-para-que-dure-mas-tiempo',
                'hero_title' => 'Consejos para el mantenimiento de pisos LED y un mejor rendimiento',
                'cover_subtitle' => 'Aprende a realizar el mantenimiento de pisos LED para conservar su brillo, evitar daños y asegurar un funcionamiento óptimo.',
                'meta_title' => 'Cómo cuidar tu piso LED | Mantenimiento de pisos LED',
                'meta_description' => 'Descubre cómo cuidar un piso LED y conoce las mejores prácticas de mantenimiento de pisos LED para mantener su calidad, brillo y rendimiento.',
                'description' => 'Los [custom:https://yuntaspublicidad.com/productos/pistas-de-baile-led/|[product:pisos-led pisos LED]  se han convertido en una de las soluciones tecnológicas más utilizadas en eventos, ferias, activaciones de marca, centros comerciales y espacios publicitarios gracias a su capacidad para proyectar contenido dinámico y crear experiencias visuales de alto impacto. Su diseño innovador permite captar la atención del público y transformar cualquier espacio en una experiencia más atractiva e interactiva.

Sin embargo, para mantener la calidad de imagen, el brillo y el correcto funcionamiento de un piso LED, es fundamental realizar un adecuado mantenimiento de pisos LED. La limpieza periódica, el uso responsable y el cuidado de sus componentes ayudan a prevenir daños y prolongar el rendimiento del equipo. En este artículo descubrirás recomendaciones prácticas para cuidar tu piso LED y mantenerlo en excelentes condiciones durante más tiempo.',
                'benefits' => [
                    'Asegura una instalación adecuada del piso LED sobre superficies niveladas y con conexiones eléctricas estables.',
                    'Realiza una limpieza periódica utilizando paños secos o ligeramente húmedos, evitando productos abrasivos.',
                    'Respeta el peso máximo permitido y guarda los módulos en un espacio seco y protegido cuando no estén en uso.'
                ],
                'testimonial' => '“¡Me encanta!, desde que seguí las recomendaciones el piso LED funciona sin problemas en cada evento y siempre mantiene una excelente calidad visual.Súper recomendado”
-Enrique Luis Calamullo-'

            ],

            [
                'product_id' => 6,
                'title' => '¿Por qué los letreros Neón LED están en tendencia y cómo pueden ayudar a tu negocio?',
                'slug' => 'por-que-los-letreros-neon-led-estan-en-tendencia',
                'hero_title' => 'La tendencia que está transformando la imagen de los negocios',
                'cover_subtitle' => 'Descubre por qué cada vez más marcas, tiendas y restaurantes utilizan letreros Neón LED para atraer clientes y destacar su imagen.',
                'meta_title' => '¿Por qué los letreros Neón LED están en tendencia?',
                'meta_description' => 'Conoce por qué los letreros Neón LED son una tendencia para negocios, restaurantes y tiendas que buscan destacar y atraer más clientes.',
                'description' => 'Los [custom:https://yuntaspublicidad.com/productos/letreros-neon-led/|[product Letreros Neón LED]han pasado de ser un simple elemento decorativo a convertirse en una poderosa herramienta para atraer clientes y fortalecer la identidad de una marca. Hoy es común encontrarlos en restaurantes, cafeterías, tiendas, oficinas e incluso eventos, gracias a su capacidad para llamar la atención y crear espacios modernos que invitan a tomar fotografías y compartirlas en redes sociales.

Pero, ¿qué tienen de especial y por qué tantas empresas están apostando por esta tecnología? En este artículo descubrirás las principales ventajas de los letreros Neón LED, cómo pueden ayudarte a destacar frente a la competencia y por qué se han convertido en una de las tendencias más populares para negocios de todos los tamaños.',
                'benefits' => [
                    'Crea espacios más atractivos y modernos que ayuden a captar la atención de clientes potenciales.',
                    'Refuerza la identidad de tu marca con diseños personalizados que destacan frente a la competencia.',
                    'Disfruta de una solución duradera, eficiente y adaptable a distintos tipos de negocios y espacios.'
                ],
                'testimonial' => 'Buscábamos una forma de hacer que nuestro negocio destacara más y los letreros Neón LED fueron la elección perfecta. Logramos una imagen moderna, llamativa y acorde con nuestra marca. - Andrea Salazar'

            ],

            [
                'product_id' => 4,
                'title' => 'Letreros en MDF que hacen brillar tu marca.',
                'slug' => 'letreros-en-mdf-que-hacen-brillar-tu-marca',
                'hero_title' => 'Letreros en MDF que hacen brillar tu marca.',
                'cover_subtitle' => 'Detalles que hacen brillar tu marca',
                'meta_title' => 'Letras Pintadas en MDF para Negocios y Eventos',
                'meta_description' => 'Son elaboradas con un corte preciso y detalles cuidadosos.',
                'description' => 'Nuestros [product:letras-pintadas-mdf|letreros pintados en MDF] están diseñados para que tu marca no pase desapercibida. Combinamos creatividad, precisión y acabados de alta calidad para crear piezas que comunican, atraen y venden. Si buscas que tu negocio se vea profesional, moderno y memorable, este es el primer paso para destacar.',
                'benefits' => [
                    'Aumentan la visibilidad de tu negocio y atraen más clientes.',
                    'Refuerzan tu imagen profesional desde el primer vistazo.',
                    'Hacen que tu marca se vea memorable y diferente a la competencia.'
                ],
                'testimonial' => 'Invertir en un letrero no es un gasto, es una decisión estratégica: tu marca se vuelve visible, confiable y recordada. Si tu negocio quiere crecer, empieza por cómo se muestra al mundo.',
                'video_url' => 'https://www.youtube.com/watch?v=9ohlxUhyD5U'
            ]

        ];

        foreach ($blogs as $data) {

            $blog = Blog::firstOrCreate(

                [
                    'slug' => $data['slug']
                ],

                [
                    'product_id' => $data['product_id'],

                    'title' => $data['title'],

                    'slug' => $data['slug'],

                    'hero_title' => $data['hero_title'],

                    'cover_subtitle' => $data['cover_subtitle'],

                    'status' => 'published',

                    'meta_title' => $data['meta_title'],

                    'meta_description' => $data['meta_description'],

                    'video_url' => $data['video_url'] ?? null,
                ]
            );

            // DESCRIPCIÓN
            BlogContentText::firstOrCreate(

                [
                    'blog_id' => $blog->id,
                    'slot_id' => 1
                ],

                [
                    'content' => $data['description']
                ]
            );

            // BENEFICIOS
            foreach ($data['benefits'] as $benefit) {

                BlogContentItem::firstOrCreate(

                    [
                        'blog_id' => $blog->id,
                        'slot_id' => 2,
                        'text' => $benefit
                    ]
                );
            }

            // TESTIMONIOS
            BlogContentText::firstOrCreate(

                [
                    'blog_id' => $blog->id,
                    'slot_id' => 3
                ],

                [
                    'content' => $data['testimonial']
                ]
            );
        }
    }
}
