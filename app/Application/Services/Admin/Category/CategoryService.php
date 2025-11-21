<?php

namespace App\Application\Services\Admin\Category;

use App\Application\DTOs\Admin\Category\CategoryDTO;
use App\Models\Category;

class CategoryService
{
    public function create(CategoryDTO $dto): Category
    {
        return Category::create([
            'name' => $dto->name,
            'description' => $dto->description,
        ]);
    }

    public function update(CategoryDTO $dto, Category $category): Category
    {
        $category->update([
            'name' => $dto->name,
            'description' => $dto->description,
        ]);
        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    public function all()
    {
        return Category::all();
    }
}
