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
        \App\Domain\Repositories\Blog\BlogRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\Repositories\Blog\EloquentBlogRepository::class,        
        // \App\Domain\Repositories\LeadRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\LeadRepository::class,
        // \App\Domain\Repositories\ClaimRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\ClaimRepository::class,
        \App\Domain\Repositories\CRM\LeadRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\Repositories\CRM\EloquentLeadRepository::class,
        // Agrega esto al array $bindings:
        \App\Domain\Repositories\Category\CategoryRepositoryInterface::class => \App\Infrastructure\Persistence\Eloquent\Repositories\Category\EloquentCategoryRepository::class,
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
