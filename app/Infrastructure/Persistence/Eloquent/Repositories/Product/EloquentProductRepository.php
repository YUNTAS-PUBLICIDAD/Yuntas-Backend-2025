<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Product;

use App\Domain\Repositories\Product\ProductRepositoryInterface;
use App\Models\Product;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function save(array $data): Product
    {
        return Product::create($data);
    }
    public function paginate(int $perPage = 10)
{
    return \App\Models\Product::with(['images.slot', 'contentItems.slot'])
        ->where('status', 'active') // Opcional: solo activos
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
}

public function findById(int $id): ?\App\Models\Product
{
    return \App\Models\Product::with(['images.slot', 'contentItems.slot'])->find($id);
}

public function update(int $id, array $data): bool
{
    $product = $this->findById($id);
    return $product ? $product->update($data) : false;
}

public function delete(int $id): bool
{
    return \App\Models\Product::destroy($id) > 0;
}
}