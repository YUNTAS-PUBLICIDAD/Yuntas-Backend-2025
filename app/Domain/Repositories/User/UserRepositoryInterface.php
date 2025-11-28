<?php

namespace App\Domain\Repositories\User;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getAll(int $perPage = 10);
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): User;
    public function delete(int $id): bool;
}