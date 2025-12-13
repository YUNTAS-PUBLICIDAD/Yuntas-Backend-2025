<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $service;
    protected CategoryRepositoryInterface $repository;

    public function __construct(CategoryService $service, CategoryRepositoryInterface $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        $categories = $this->repository->all();
        return CategoryResource::collection($categories);
    }

    public function show($id)
    {
        $category = $this->repository->find($id);
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $request)
    {
        $dto = new CategoryDTO(
            $request->input('name'),
            $request->input('description')
        );
        $category = $this->service->create($dto);
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->repository->find($id);
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        $dto = new CategoryDTO(
            $request->input('name'),
            $request->input('description')
        );
        $updated = $this->service->update($dto, $category);
        return new CategoryResource($updated);
    }

    public function destroy($id)
    {
        $category = $this->repository->find($id);
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        $this->service->delete($category);
        return response()->json(['message' => 'Categoría eliminada correctamente']);
    }
}
