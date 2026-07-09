<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateStepSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('template_steps')->insert([
            [
                'template_id' => 1,
                'step' => '1',
                'delay_value' => 0,
                'delay_unit' => 'minutes',
                'active' => 1,
            ],
            [
                'template_id' => 1,
                'step' => '2',
                'delay_value' => 4,
                'delay_unit' => 'hours',
                'active' => 1,
            ],
            [
                'template_id' => 1,
                'step' => '3',
                'delay_value' => 3,
                'delay_unit' => 'days',
                'active' => 1,
            ],
        ]);
    }
}