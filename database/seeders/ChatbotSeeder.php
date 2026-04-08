<?php

namespace Database\Seeders;

use App\Models\ChatbotAction;
use App\Models\ChatbotActionCondition;
use App\Models\ChatbotAnswer;
use App\Models\ChatbotIntent;
use App\Models\ChatbotQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChatbotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


       /*
        |--------------------------------------------------------------------------
        | 🧠 INTENT: SALUDO
        |--------------------------------------------------------------------------
        */
        $intentSaludo = ChatbotIntent::firstOrCreate([
            'name' => 'saludo'
        ]);

        $qSaludo = ChatbotQuestion::updateOrCreate(
            [
                'intent_id' => $intentSaludo->id,
                'question_text' => 'saludo usuario',
            ],
            [
                'keywords' => ['hola', 'buenas', 'hello']
            ]
        );

        ChatbotAnswer::updateOrCreate(
            [
                'question_id' => $qSaludo->id,
            ],
            [
                'answer_text' => 'Hola 👋 {{user.name|amigo}} Bienvenido a Yuntas Publicidad. ¿Buscas cotizar un proyecto o conocer nuestros servicios?'
            ]
        );

        $intentSmalltalk = ChatbotIntent::firstOrCreate([
          'name' => 'smalltalk'
        ]);

        $qSmalltalk = ChatbotQuestion::updateOrCreate(
          [
            'intent_id' => $intentSmalltalk->id,
            'question_text' => 'interacción casual'
          ],
          [
            'keywords' => [
              'como estas',
              'como te va',
              'todo bien',
              'que tal',
              'como andas',
              'estas bien'
            ]
          ]
        );

        ChatbotAnswer::updateOrCreate(
          [
              'question_id' => $qSmalltalk->id
          ],
          [
            'answer_text' => 'Todo bien por aquí 👍 ¿En qué puedo ayudarte?'
          ]
        );

        /*
        |--------------------------------------------------------------------------
        | 🧠 INTENT: COMPRA (ENTRA A ESM)
        |--------------------------------------------------------------------------
        */
        $intentCompra = ChatbotIntent::firstOrCreate([
            'name' => 'compra'
        ]);

        $qCompra = ChatbotQuestion::updateOrCreate(
            [
                'intent_id' => $intentCompra->id,
                'question_text' => 'interes compra',
            ],
            [
                'keywords' => ['comprar', 'cotizacion', 'cotizar', 'proyecto', 'presupuesto']
            ]
        );

        $answerCompra = ChatbotAnswer::updateOrCreate(
            [
                'question_id' => $qCompra->id,
            ],
            [
                'answer_text' => 'Perfecto {{user.name|amigo}} 👍 trabajamos con letreros luminosos, neón LED y soluciones visuales. ¿Qué tipo de proyecto tienes?'
            ]
        );

        // CAMBIO ESTADO (ESM)
        $actionSales = ChatbotAction::updateOrCreate(
            [
                // 'name' => 'set_sales',
                'name' => 'ask_project_type'
            ],
            [
                'trigger_type' => 'after_answer',
                'action_type' => 'update_context',
                'parameters' => [
                    // 'key' => 'conversation.step',
                    // 'value' => 'sales'
                    'key' => 'conversation.state',
                    'value' => 'asking_project_type'
                ]
            ]
        );

        $answerCompra->actions()->syncWithoutDetaching([
            $actionSales->id => ['priority' => 1, 'is_active' => 1]
        ]);

        /*
        |--------------------------------------------------------------------------
        | 🧠 INTENT: SERVICIOS
        |--------------------------------------------------------------------------
        */
        $intentServicios = ChatbotIntent::firstOrCreate([
            'name' => 'servicios'
        ]);

        $qServicios = ChatbotQuestion::updateOrCreate(
            [
                'intent_id' => $intentServicios->id,
                'question_text' => 'servicios disponibles',
            ],
            [
                'keywords' => ['servicios', 'que hacen', 'ofrecen']
            ]
        );

        ChatbotAnswer::updateOrCreate(
            [
                'question_id' => $qServicios->id,
            ],
            [
                'answer_text' => 'Ofrecemos letreros luminosos, iluminación LED, neón personalizado y diseño de espacios comerciales.'
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 🧠 INTENT: PRECIO (CONDICIONAL)
        |--------------------------------------------------------------------------
        */
        $intentPrecio = ChatbotIntent::firstOrCreate([
            'name' => 'precio'
        ]);

        $qPrecio = ChatbotQuestion::updateOrCreate(
            [
                'intent_id' => $intentPrecio->id,
                'question_text' => 'consulta precio',
            ],
            [
                'keywords' => ['precio', 'costo', 'cuanto cuesta']
            ]
        );

        $answerPrecio = ChatbotAnswer::updateOrCreate(
            [
                'question_id' => $qPrecio->id,
            ],
            [
                'answer_text' => 'El precio depende del tipo de proyecto {{user.name|amigo}}. ¿Qué necesitas exactamente?'
            ]
        );

        $actionPrecio = ChatbotAction::updateOrCreate(
            [
                'name' => 'precio_only_sales',
            ],
            [
                'trigger_type' => 'after_answer',
                'action_type' => 'log'
            ]
        );

        ChatbotActionCondition::updateOrCreate(
            [
                'action_id' => $actionPrecio->id,
                // 'field' => 'context.conversation.step',
                'field' => 'context.conversation.state',
                'operator' => '='
            ],
            [
                // 'value' => 'sales'
                'value' => 'asking_project_type'
            ]
        );

        $answerPrecio->actions()->syncWithoutDetaching([
            $actionPrecio->id => ['priority' => 1, 'is_active' => 1]
        ]);

        /*
        |--------------------------------------------------------------------------
        | 🧠 INTENT: ASESOR
        |--------------------------------------------------------------------------
        */
        $intentAsesor = ChatbotIntent::firstOrCreate([
          'name' => 'asesor'
        ]);

        $qAsesor = ChatbotQuestion::updateOrCreate(
          [
            'intent_id' => $intentAsesor->id,
            'question_text' => 'hablar con asesor'
          ],
          [
            'keywords' => ['asesor', 'hablar con alguien', 'whatsapp', 'atencion']
          ]
        );


     $answerAsesor =  ChatbotAnswer::updateOrCreate(
        [
          'question_id' => $qAsesor->id
        ],
        [
          'answer_text' => 'Te contacto con un asesor ahora mismo 👇'
        ]
        );

        $actionWhatsapp = ChatbotAction::updateOrCreate(
        [
          'name' => 'send_whatsapp'
        ],
        [
          'trigger_type' => 'after_answer',
          'action_type' => 'send_metadata',
          'parameters' => [
            'type' => 'whatsapp'
          ]
        ]
        );

        $answerAsesor->actions()->syncWithoutDetaching([
        $actionWhatsapp->id => ['priority' => 1, 'is_active' => 1]
        ]);
        /*
        |--------------------------------------------------------------------------
        | 🧠 INTENT: CONTACTO
        |--------------------------------------------------------------------------
        */
        $intentContacto = ChatbotIntent::firstOrCreate([
            'name' => 'contacto'
        ]);

        $qContacto = ChatbotQuestion::updateOrCreate(
            [
                'intent_id' => $intentContacto->id,
                'question_text' => 'info contacto',
            ],
            [
                'keywords' => ['contacto', 'correo', 'ubicacion', 'oficina', 'direccion', 'donde estan', 'donde queda', 'donde se ubican', 'tienen local', 'mapa', 'como los ubico']
            ]
        );

        $answerContacto = ChatbotAnswer::updateOrCreate(
            [
                'question_id' => $qContacto->id,
            ],
            [
                'answer_text' => 'Puedes ver toda nuestra información aquí 👇'
            ]
        );

        $actionContact = ChatbotAction::updateOrCreate(
          [
            'name' => 'send_contact_page'
          ],
          [
          'trigger_type' => 'after_answer',
          'action_type' => 'send_metadata',
          'parameters' => [
            'type' => 'contact_page',
            'url' => '/contacto'
          ]
          ]
        );

        $answerContacto->actions()->syncWithoutDetaching([
        $actionContact->id => ['priority' => 1, 'is_active' => 1]
        ]);
        /*
    |--------------------------------------------------------------------------
    | 🧠 INTENT: AGRADECIMIENTO
    |--------------------------------------------------------------------------
    */

    $intentAgradecimiento = ChatbotIntent::firstOrCreate([
      'name' => 'agradecimiento'
    ]);

    $qGracias = ChatbotQuestion::updateOrCreate(
      [
        'intent_id' => $intentAgradecimiento->id,
        'question_text' => 'agradecimiento usuario',
      ],
      [
        'keywords' => ['gracias', 'thanks', 'ok gracias', 'perfecto gracias']
      ]
    );

    ChatbotAnswer::updateOrCreate(
      [
        'question_id' => $qGracias->id
      ],
      [
        'answer_text' => 'Con gusto {{user.name|}} 😊 ¿Necesitas algo más?'
      ]
    );
    }
}
