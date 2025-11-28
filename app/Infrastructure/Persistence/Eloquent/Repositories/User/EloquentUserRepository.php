<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\User;

use App\Domain\Repositories\User\UserRepositoryInterface;
use App\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function getAll(int $perPage = 10)
    {
        // Traemos roles para evitar N+1
        return User::with('roles')->latest()->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete(int $id): bool
    {
        return User::destroy($id) > 0;
    }
}