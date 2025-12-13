<?php

namespace App\Domain\Repositories;

use App\Application\DTOs\Support\SendEmailDTO;

interface EmailRepositoryInterface
{
    public function send(SendEmailDTO $dto): bool;
}
