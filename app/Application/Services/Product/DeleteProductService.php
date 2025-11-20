<?php

namespace App\Application\Services\Product;

use App\Domain\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteProductService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        $product = $this->repository->findById($id);

        if (!$product) {
            throw new ModelNotFoundException("Producto no encontrado");
        }

        
        $this->repository->delete($id);
    }
}