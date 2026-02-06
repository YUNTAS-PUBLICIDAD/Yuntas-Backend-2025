<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->timezone('America/Lima');
        $schedule->command('queue:work --queue=default --stop-when-empty --max-time=50')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->sendOutputTo('/dev/null');

        $schedule->command('queue:work --queue=deployments --stop-when-empty --max-time=50')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->sendOutputTo('/dev/null');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}