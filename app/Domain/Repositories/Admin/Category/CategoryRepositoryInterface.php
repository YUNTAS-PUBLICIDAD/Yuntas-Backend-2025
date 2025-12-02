<?php

namespace App\Domain\Repositories\Admin\Category;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function getAll(int $perPage = 10);
    public function findById(int $id): ?Category;
    public function save(array $data): Category;
    public function update(int $id, array $data): Category;
    public function delete(int $id): bool;
}