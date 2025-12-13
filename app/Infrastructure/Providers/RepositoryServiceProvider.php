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
        \App\Domain\Repositories\Product\ProductRepositoryInterface::class 
            => \App\Infrastructure\Persistence\Eloquent\Repositories\Product\EloquentProductRepository::class,

        \App\Domain\Repositories\Blog\BlogRepositoryInterface::class 
            => \App\Infrastructure\Persistence\Eloquent\Repositories\Blog\EloquentBlogRepository::class,

        \App\Domain\Repositories\CRM\LeadRepositoryInterface::class 
            => \App\Infrastructure\Persistence\Eloquent\Repositories\CRM\EloquentLeadRepository::class,

        \App\Domain\Repositories\Support\ClaimRepositoryInterface::class 
            => \App\Infrastructure\Persistence\Eloquent\Repositories\Support\EloquentClaimRepository::class,

        \App\Domain\Repositories\User\UserRepositoryInterface::class 
            => \App\Infrastructure\Persistence\Eloquent\Repositories\User\EloquentUserRepository::class,

        \App\Domain\Repositories\Support\ContactRepositoryInterface::class 
            => \App\Infrastructure\Persistence\Eloquent\Repositories\Support\EloquentContactRepository::class,

        \App\Domain\Repositories\Category\CategoryRepositoryInterface::class 
            => \App\Infrastructure\Persistence\Eloquent\Repositories\Category\EloquentCategoryRepository::class,

        // agregar el ejemplo del repositorio de email
        \App\Domain\Repositories\Support\EmailRepositoryInterface::class
            => \App\Infrastructure\Persistence\Eloquent\Repositories\Support\EmailRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
