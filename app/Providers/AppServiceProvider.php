<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar los Service Providers de Infrastructure
        $this->app->register(\App\Infrastructure\Providers\DomainServiceProvider::class);
        $this->app->register(\App\Infrastructure\Providers\RepositoryServiceProvider::class);
        $this->app->register(\App\Infrastructure\Providers\EventServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configuración para MySQL string length
        Schema::defaultStringLength(191);
    }
}
