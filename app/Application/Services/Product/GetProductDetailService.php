<?php

namespace App\Application\Services\Product;

use App\Domain\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetProductDetailService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(string $slug)
    {
        $product = $this->repository->findBySlug($slug);

        if (!$product) {
            throw new ModelNotFoundException("Producto no encontrado con slug: $slug");
        }

        return $product;
    }
}