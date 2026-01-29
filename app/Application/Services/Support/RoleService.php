<?php

namespace App\Application\Services\Support;
use App\Domain\Repositories\Support\RoleRepositoryInterface;

class RoleService
{
    public function __construct(
        private RoleRepositoryInterface $repository
    ) {}

    public function getAll(int $perPage = 20)
    {
        return $this->repository->getAll($perPage);
    }

}