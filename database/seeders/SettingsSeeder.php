<?php

namespace Database\Seeders;

use App\Models\ChatbotSetting;
use App\Models\ContactSetting;
use App\Models\GeneralSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      // =========================
              // GENERAL
              // =========================
              GeneralSetting::updateOrCreate(
                  ['id' => 1],
                  [
                      'company_name' => 'Yuntas',
                      'logo_light' => null,
                      'logo_dark' => null,
                      'theme' => 'light',
                  ]
              );

              // =========================
              // CONTACT
              // =========================
              ContactSetting::updateOrCreate(
                  ['id' => 1],
                  [
                      'phone' => null,
                      'email' => null,
                      'address' => null,
                      'business_hours' => null,
                      'social_links' => null,
                      'whatsapp_message' => 'Hola, quiero más información',
                      'show_in_footer' => true,
                      'show_contact_page' => true,
                      'map_url' => null,
                  ]
              );

              // =========================
              // CHATBOT
              // =========================
              ChatbotSetting::updateOrCreate(
                  ['id' => 1],
                  [
                      'enabled' => true,
                      'primary_color' => '#6366F1',
                      'secondary_color' => null,
                      'icon' => null,
                      'position' => 'bottom-right',
                      'welcome_message' => 'Hola 👋 ¿En qué podemos ayudarte?',
                      'show_delay_seconds' => 3,
                      'auto_close_seconds' => null,
                  ]
              );
    }
}
