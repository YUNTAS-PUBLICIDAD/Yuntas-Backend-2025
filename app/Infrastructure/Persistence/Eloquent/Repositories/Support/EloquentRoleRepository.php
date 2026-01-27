<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Support;

use App\Domain\Repositories\Support\RoleRepositoryInterface;
use App\Models\Role;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function getAll(int $perPage = 20)
    {
        return Role::latest()
            ->paginate($perPage);
    }
}