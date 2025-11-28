<?php

namespace App\Domain\Repositories\Support;

use App\Models\ContactMessage;

interface ContactRepositoryInterface
{
    public function getAll(int $perPage = 20);
    public function findById(int $id): ?ContactMessage;
    public function save(array $data): ContactMessage;
    public function delete(int $id): bool;
}