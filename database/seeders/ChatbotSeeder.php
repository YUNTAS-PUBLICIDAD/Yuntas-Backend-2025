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

    // // Intent: Nombre
    // $intentNombre = ChatbotIntent::firstOrCreate([
    //   'name' => 'nombre'
    // ]);

    // $qNombre = ChatbotQuestion::updateOrCreate(
    //   [
    //     'intent_id' => $intentNombre->id,
    //     'question_text' => 'capturar nombre'
    //   ],
    //   [
    //     'keywords' => ['me llamo', 'soy', 'mi nombre es', 'nombre', 'llamo']
    //   ]
    // );

    // $answerNombre = ChatbotAnswer::updateOrCreate(
    //   [
    //     'question_id' => $qNombre->id
    //   ],
    //   [
    //     // 'answer_text' => 'Mucho gusto {{user.name|amigo}} 👌 ¿En qué puedo ayudarte?'
    //     'answer_text' => 'Perfecto, {{user.name|amigo}} 👍'
    //   ]
    // );
    // // Acción
    // $actionSaveName = ChatbotAction::updateOrCreate(
    //   ['name' => 'save_name'],
    //   [
    //     'trigger_type' => 'after_answer',
    //     'action_type' => 'update_context',
    //     'parameters' => [
    //       'key' => 'user.name',
    //       'value' => '__from_message__'
    //     ]
    //   ]
    // );

    // $answerNombre->actions()->syncWithoutDetaching([
    //   $actionSaveName->id => ['priority' => 1, 'is_active' => 1]
    // ]);

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
        | 🧠 INTENT: CONTACTO
        |--------------------------------------------------------------------------
        */
        $intentContacto = ChatbotIntent::firstOrCreate([
            'name' => 'contacto'
        ]);

        $qContacto = ChatbotQuestion::updateOrCreate(
            [
                'intent_id' => $intentContacto->id,
                'question_text' => 'contacto cliente',
            ],
            [
                'keywords' => ['contacto', 'telefono', 'asesor', 'hablar']
            ]
        );

        ChatbotAnswer::updateOrCreate(
            [
                'question_id' => $qContacto->id,
            ],
            [
                'answer_text' => 'Déjame tus datos y un asesor te contactará.'
            ]
        );
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
