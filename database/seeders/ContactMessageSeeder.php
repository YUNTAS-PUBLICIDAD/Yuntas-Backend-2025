<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactMessage;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [

        [
            'first_name' => 'carlos',
            'last_name' => 'miranda',
            'phone' => '935548619',
            'district' => 'tambo',
            'request_detail' => 'cotizacion',
            'message' => 'vivo en huancayo y necesito cotizar las mochilas holograficas 3d para publicidad me dan informacion por favor'
        ],
        
        [
            'first_name' => 'Yudith',
            'last_name' => 'Pacco',
            'phone' => '931840727',
            'district' => 'Santa Anita',
            'request_detail' => 'Cotizar',
            'message' => 'Me gustaria cotizar un producto'
        ],

        [
            'first_name' => 'Rossy',
            'last_name' => 'Peña',
            'phone' => '945152256',
            'district' => 'La Molina',
            'request_detail' => 'Información',
            'message' => 'Quiero información sobre un producto'
        ],

        [
            'first_name' => 'Angie',
            'last_name' => 'Lopez',
            'phone' => '956546156',
            'district' => 'Ate',
            'request_detail' => 'Información',
            'message' => 'Quiero informacion sobre las luces led'
        ],

        [
            'first_name' => 'alejandro',
            'last_name' => 'camilo',
            'phone' => '990137296',
            'district' => 'chancay',
            'request_detail' => 'proforma',
            'message' => 'deseo saber costo de 12m2 de piso leds  e informes'
        ],

        [
            'first_name' => 'Yuli',
            'last_name' => 'Ponce',
            'phone' => '955556512',
            'district' => 'Ate',
            'request_detail' => 'Cotizar',
            'message' => 'quiero un cotizacion sobre..'
        ],

        [
            'first_name' => 'Daniel',
            'last_name' => 'Bravo',
            'phone' => '941566514',
            'district' => 'La Molina',
            'request_detail' => 'Información',
            'message' => 'Quiero más información'
        ],

        [
            'first_name' => 'Angie',
            'last_name' => 'Tembladera',
            'phone' => '914503174',
            'district' => 'Santa Anita',
            'request_detail' => 'Información',
            'message' => 'Prueba de detalle'
        ],

        [
            'first_name' => 'ASD',
            'last_name' => 'SD',
            'phone' => '949395083',
            'district' => '',
            'request_detail' => '',
            'message' => 'ASDSADSADASDSADAS'
        ],

        [
            'first_name' => 'Daniel Ricardo',
            'last_name' => 'Rivera Cordova',
            'phone' => '917373577',
            'district' => 'LIMA',
            'request_detail' => 'COMPRA',
            'message' => 'COMPRAS PARA EMPRESA'
        ],

        [
            'first_name' => 'Daniel Ricardo',
            'last_name' => 'Rivera Cordova',
            'phone' => '917373577',
            'district' => 'LIMA',
            'request_detail' => 'COMPRA',
            'message' => 'COMPRAAAAAAAAAAAAAAAAAAAAAAA'
        ],

        [
            'first_name' => 'BIBIANA',
            'last_name' => 'PEREZ',
            'phone' => '999007750',
            'district' => 'MERIDA YUCATAN',
            'request_detail' => 'MUROS PARA EXTERIOR',
            'message' => 'REQUIERO DE ALGO RAPIDO LIMPIO Y FACIL DE MONTAR ES UNA AREA PARA TENER MAS PRIVACIDAD, MI TEL ES 9999007750 ES DE MERIDA YUCATAN, GRACIAS'
        ],

        [
            'first_name' => 'Elizabeth',
            'last_name' => 'Santiago',
            'phone' => '551599755',
            'district' => 'estado de mexico',
            'request_detail' => 'cotizacion de panatalla led 100x80 cm',
            'message' => 'es para interior de un núcleo medico y promocionar servicios con actualizaciones'
        ],

        [
            'first_name' => 'Adriana',
            'last_name' => 'Diestra',
            'phone' => '917911363',
            'district' => 'Ate',
            'request_detail' => 'Reforma de casa de juegos arcade',
            'message' => 'Quiero informacion de precios de techos led para mejorar mi arcade'
        ],

    ];

    foreach ($messages as $data) {

        ContactMessage::create($data);

    }
    }
}
