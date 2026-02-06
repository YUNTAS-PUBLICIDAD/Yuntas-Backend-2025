<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::timezone('America/Lima');
Schedule::command('queue:work --queue=default --stop-when-empty --max-time=50')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->sendOutputTo(storage_path('logs/queue-default.log'))
            ->emailOutputOnFailure();

Schedule::command('queue:work --queue=deployments --stop-when-empty --max-time=50')
        ->everyMinute()
        ->withoutOverlapping()
        ->runInBackground()
        ->sendOutputTo(storage_path('logs/queue-deployments.log'))
        ->emailOutputOnFailure();
