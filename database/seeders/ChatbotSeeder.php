<?php

namespace Database\Seeders;

use App\Models\ChatbotAction;
use App\Models\ChatBotActionCondition;
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

        $qSaludo = ChatbotQuestion::firstOrCreate(
            [
                'intent_id' => $intentSaludo->id,
                'question_text' => 'saludo usuario',
            ],
            [
                'keywords' => ['hola', 'buenas', 'hello']
            ]
        );

        ChatbotAnswer::firstOrCreate(
            [
                'question_id' => $qSaludo->id,
            ],
            [
                'answer_text' => 'Hola {{context.user_name ?? "👋"}} Bienvenido a Yuntas Publicidad. ¿Buscas cotizar un proyecto o conocer nuestros servicios?'
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 🧠 INTENT: COMPRA
        |--------------------------------------------------------------------------
        */
        $intentCompra = ChatbotIntent::firstOrCreate([
            'name' => 'compra'
        ]);

        $qCompra = ChatbotQuestion::firstOrCreate(
            [
                'intent_id' => $intentCompra->id,
                'question_text' => 'interes compra',
            ],
            [
                'keywords' => ['comprar', 'quiero', 'cotizar', 'proyecto']
            ]
        );

        $answerCompra = ChatbotAnswer::firstOrCreate(
            [
                'question_id' => $qCompra->id,
            ],
            [
                'answer_text' => 'Perfecto {{context.user_name ?? ""}} 👍 trabajamos con letreros luminosos, neón LED y soluciones visuales. ¿Qué tipo de proyecto tienes?'
            ]
        );

        $actionSales = ChatbotAction::firstOrCreate(
            [
                'name' => 'set_sales',
            ],
            [
                'trigger_type' => 'after_answer',
                'action_type' => 'update_context',
                'parameters' => [
                    'key' => 'step',
                    'value' => 'sales'
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

        $qServicios = ChatbotQuestion::firstOrCreate(
            [
                'intent_id' => $intentServicios->id,
                'question_text' => 'servicios disponibles',
            ],
            [
                'keywords' => ['servicios', 'que hacen', 'ofrecen']
            ]
        );

        ChatbotAnswer::firstOrCreate(
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

        $qPrecio = ChatbotQuestion::firstOrCreate(
            [
                'intent_id' => $intentPrecio->id,
                'question_text' => 'consulta precio',
            ],
            [
                'keywords' => ['precio', 'costo', 'cuanto cuesta']
            ]
        );

        $answerPrecio = ChatbotAnswer::firstOrCreate(
            [
                'question_id' => $qPrecio->id,
            ],
            [
                'answer_text' => 'El precio depende del tipo de proyecto {{context.user_name ?? ""}}. ¿Qué necesitas exactamente?'
            ]
        );

        $actionPrecio = ChatbotAction::firstOrCreate(
            [
                'name' => 'precio_only_sales',
            ],
            [
                'trigger_type' => 'after_answer',
                'action_type' => 'log'
            ]
        );

        ChatBotActionCondition::firstOrCreate(
            [
                'action_id' => $actionPrecio->id,
                'field' => 'context.step',
                'operator' => '='
            ],
            [
                'value' => 'sales'
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

        $qContacto = ChatbotQuestion::firstOrCreate(
            [
                'intent_id' => $intentContacto->id,
                'question_text' => 'contacto cliente',
            ],
            [
                'keywords' => ['contacto', 'telefono', 'asesor', 'hablar']
            ]
        );

        ChatbotAnswer::firstOrCreate(
            [
                'question_id' => $qContacto->id,
            ],
            [
                'answer_text' => 'Puedes contactarnos al +51 912 849 782 o dejarme tus datos y un asesor te escribirá.'
            ]
        );
    }
}
