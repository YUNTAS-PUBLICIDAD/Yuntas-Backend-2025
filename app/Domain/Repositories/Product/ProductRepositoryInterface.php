<?php

namespace App\Domain\Repositories\Product; 

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function save(array $data): Product;
    public function paginate(int $perPage = 10);
    public function findById(int $id): ?\App\Models\Product;
    public function update(int $id, array $data): bool;     
    public function delete(int $id): bool;
}