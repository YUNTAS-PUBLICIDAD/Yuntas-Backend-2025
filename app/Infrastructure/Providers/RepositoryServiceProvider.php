<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        // Interface => Implementation
        \App\Domain\Repositories\Product\ProductRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\Repositories\Product\EloquentProductRepository::class,
        
        // \App\Domain\Repositories\ProductRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\ProductRepository::class,
        // \App\Domain\Repositories\BlogRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\BlogRepository::class,
        // \App\Domain\Repositories\LeadRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\LeadRepository::class,
        // \App\Domain\Repositories\ClaimRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\ClaimRepository::class,
    ];

    /**
     * Register repository services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap repository services.
     */
    public function boot(): void
    {
        //
    }
}
