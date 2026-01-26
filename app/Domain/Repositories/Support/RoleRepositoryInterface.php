<?php

namespace App\Domain\Repositories\Support;

interface RoleRepositoryInterface
{
    public function getAll(int $perPage = 20);
}