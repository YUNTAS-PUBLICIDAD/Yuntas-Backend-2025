<?php

namespace App\Application\Services\Product;

use App\Domain\Repositories\Product\ProductRepositoryInterface;

class GetProductCatalogService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(int $perPage = 10)
    {
        return $this->repository->paginate($perPage);
    }
}