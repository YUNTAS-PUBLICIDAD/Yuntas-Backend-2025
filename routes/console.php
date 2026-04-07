<?php

use App\Models\ChatbotConversation;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ejecutar en desarrollo php artisan schedule:work
Schedule::call(function () {
  ChatbotConversation::where('updated_at', '<', now()->subDay())->delete();
})->hourly();
