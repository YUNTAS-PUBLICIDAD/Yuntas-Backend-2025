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

        $qSaludo = ChatbotQuestion::create([
            'intent_id' => $intentSaludo->id,
            'question_text' => 'saludo usuario',
            'keywords' => ['hola', 'buenas', 'hello']
        ]);

        ChatbotAnswer::create([
            'question_id' => $qSaludo->id,
            'answer_text' => 'Hola 👋 Bienvenido a Yuntas Publicidad. ¿Buscas cotizar un proyecto o conocer nuestros servicios?'
        ]);

        /*
        |--------------------------------------------------------------------------
        | 🧠 INTENT: COMPRA / INTERÉS
        |--------------------------------------------------------------------------
        */
        $intentCompra = ChatbotIntent::firstOrCreate([
            'name' => 'compra'
        ]);

        $qCompra = ChatbotQuestion::create([
            'intent_id' => $intentCompra->id,
            'question_text' => 'interes compra',
            'keywords' => ['comprar', 'quiero', 'cotizar', 'proyecto']
        ]);

        $answerCompra = ChatbotAnswer::create([
            'question_id' => $qCompra->id,
            'answer_text' => 'Perfecto 👍 trabajamos con letreros luminosos, neón LED y soluciones visuales. ¿Qué tipo de proyecto tienes?'
        ]);

        // Acción → entrar en funnel ventas
        $actionSales = ChatbotAction::create([
            'name' => 'set_sales',
            'trigger_type' => 'after_answer',
            'action_type' => 'update_context',
            'parameters' => [
                'key' => 'step',
                'value' => 'sales'
            ]
        ]);

        $answerCompra->actions()->sync([
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

        $qServicios = ChatbotQuestion::create([
            'intent_id' => $intentServicios->id,
            'question_text' => 'servicios disponibles',
            'keywords' => ['servicios', 'que hacen', 'ofrecen']
        ]);

        ChatbotAnswer::create([
            'question_id' => $qServicios->id,
            'answer_text' => 'Ofrecemos letreros luminosos, iluminación LED, neón personalizado y diseño de espacios comerciales.'
        ]);

        /*
        |--------------------------------------------------------------------------
        | 🧠 INTENT: PRECIO (CONDICIONADO 🔥)
        |--------------------------------------------------------------------------
        */
        $intentPrecio = ChatbotIntent::firstOrCreate([
            'name' => 'precio'
        ]);

        $qPrecio = ChatbotQuestion::create([
            'intent_id' => $intentPrecio->id,
            'question_text' => 'consulta precio',
            'keywords' => ['precio', 'costo', 'cuanto cuesta']
        ]);

        $answerPrecio = ChatbotAnswer::create([
            'question_id' => $qPrecio->id,
            'answer_text' => 'El precio depende del tipo de proyecto. ¿Qué necesitas exactamente?'
        ]);

        $actionPrecio = ChatbotAction::create([
            'name' => 'precio_only_sales',
            'trigger_type' => 'after_answer',
            'action_type' => 'log'
        ]);

        // 🔥 condición clave
        ChatBotActionCondition::create([
            'action_id' => $actionPrecio->id,
            'field' => 'context.step',
            'operator' => '=',
            'value' => 'sales'
        ]);

        $answerPrecio->actions()->sync([
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

        $qContacto = ChatbotQuestion::create([
            'intent_id' => $intentContacto->id,
            'question_text' => 'contacto cliente',
            'keywords' => ['contacto', 'telefono', 'hablar', 'asesor']
        ]);

        ChatbotAnswer::create([
            'question_id' => $qContacto->id,
            'answer_text' => 'Puedes contactarnos al +51 912 849 782 o dejarme tus datos y un asesor te escribirá.'
        ]);
    }
}
