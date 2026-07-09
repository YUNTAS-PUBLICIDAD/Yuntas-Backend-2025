<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateVariantSeeder extends Seeder
{
    public function run(): void
    {
        $stepId = DB::table('template_steps')->value('id');

        DB::table('template_variants')->insert([
            [
                'template_step_id' => 1,
                'channel' => 'whatsapp',
                'active' => 1,
            ],
            [
                'template_step_id' => 1,
                'channel' => 'email',
                'active' => 1,
            ],
            [
                'template_step_id' => 2,
                'channel' => 'whatsapp',
                'active' => 1,
            ],
            [
                'template_step_id' => 2,
                'channel' => 'email',
                'active' => 1,
            ],
            [
                'template_step_id' => 3,
                'channel' => 'whatsapp',
                'active' => 1,
            ],
            [
                'template_step_id' => 3,
                'channel' => 'email',
                'active' => 1,
            ],
        ]);
    }
}