<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\Support\EmailRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\Support\EmailRepository;

class EmailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(EmailRepositoryInterface::class, EmailRepository::class);
    }
}
