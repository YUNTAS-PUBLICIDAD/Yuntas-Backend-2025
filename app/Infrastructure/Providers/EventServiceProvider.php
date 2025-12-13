<?php

namespace App\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Ejemplo de eventos de dominio:
        // \App\Domain\Events\ProductCreated::class => [
        //     \App\Application\Listeners\SendProductNotification::class,
        // ],
        // \App\Domain\Events\LeadCaptured::class => [
        //     \App\Application\Listeners\SendWelcomeEmail::class,
        //     \App\Application\Listeners\NotifySalesTeam::class,
        // ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
