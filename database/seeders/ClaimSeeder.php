<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Claim;

class ClaimSeeder extends Seeder
{
    public function run(): void
    {
        $claims = [

            [
                'first_name' => 'Teves',
                'last_name' => 'Samaniego',
                'document_type_id' => 1,
                'document_number' => '78923445',
                'email' => 'joan.fernando.t.s@gmail.com',
                'phone' => '912213464',
                'purchase_date' => '2026-05-25',
                'product_id' => 3,
                'detail' => 'Se cayo mi techo y ahora me da frio en la noche',
                'claimed_amount' => 10.00,
            ],

            [
                'first_name' => 'Rossy',
                'last_name' => 'Lopez',
                'document_type_id' => 1,
                'document_number' => '74268394',
                'email' => 'rossy@gmail.com',
                'phone' => '945546822',
                'purchase_date' => '2026-02-05',
                'product_id' => 8,
                'detail' => 'El producto me llego Dañado',
                'claimed_amount' => 300.00,
            ],

            [
                'first_name' => 'yudi',
                'last_name' => 'Pacco',
                'document_type_id' => 1,
                'document_number' => '75268124',
                'email' => 'yudith@gmail.com',
                'phone' => '945678954',
                'purchase_date' => '2026-01-29',
                'product_id' => 6,
                'detail' => 'Hola quiero',
                'claimed_amount' => 200.00,
                'claim_status_id' => 2,
            ],

        ];

        foreach ($claims as $data) {

            $data['claim_type_id'] = $data['claim_type_id'] ?? 1;
            $data['claim_status_id'] = $data['claim_status_id'] ?? 1;

            Claim::create($data);
        }
    }
}