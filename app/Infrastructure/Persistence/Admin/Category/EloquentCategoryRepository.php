<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Category;

use App\Domain\Repositories\Category\CategoryRepositoryInterface;
use App\Models\Category;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(int $perPage = 10)
    {
        return Category::latest()->paginate($perPage);
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function save(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id): bool
    {
        return Category::destroy($id) > 0;
    }
}