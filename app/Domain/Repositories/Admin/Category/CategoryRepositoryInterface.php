<?php

namespace App\Domain\Repositories\Admin\Category;

use App\Application\DTOs\Admin\Category\CategoryDTO;
use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function create(CategoryDTO $dto): Category;
    public function update(CategoryDTO $dto, Category $category): Category;
    public function delete(Category $category): void;
    public function find(int $id): ?Category;
    public function all();
}
