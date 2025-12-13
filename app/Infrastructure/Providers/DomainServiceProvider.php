<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register domain services and repository bindings.
     */
    public function register(): void
    {
        // Ejemplo de binding de repositorios:
        // $this->app->bind(
        //     \App\Domain\Repositories\ProductRepositoryInterface::class,
        //     \App\Infrastructure\Persistence\Eloquent\ProductRepository::class
        // );
    }

    /**
     * Bootstrap domain services.
     */
    public function boot(): void
    {
        //
    }
}
