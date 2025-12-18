<?php

namespace App\Application\Services\Admin\Category;

use App\Application\DTOs\Admin\Category\CategoryDTO;
use App\Domain\Repositories\Admin\Category\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $repository
    ) {}

    public function getAll(int $perPage = 10)
    {
        return $this->repository->getAll($perPage);
    }

    public function getById(int $id)
    {
        $category = $this->repository->findById($id);
        if (!$category) throw new ModelNotFoundException("Categoría no encontrada");
        return $category;
    }

    public function create(CategoryDTO $dto)
    {
        return $this->repository->save([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description 
        ]);
    }

    public function update(int $id, CategoryDTO $dto)
    {
        $this->getById($id);
        
        return $this->repository->update($id, [
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description 
        ]);
    }

    public function delete(int $id): void
    {
        $this->getById($id);
        $this->repository->delete($id);
    }
}