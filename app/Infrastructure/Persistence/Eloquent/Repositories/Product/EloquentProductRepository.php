<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Product;

use App\Domain\Repositories\Product\ProductRepositoryInterface;
use App\Models\Product;

class EloquentProductRepository implements ProductRepositoryInterface
{
    /**
     * Guardar un nuevo producto (Datos básicos)
     */
    public function save(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Listar productos (Para el Admin o Catálogo)
     *  Agregamos 'categories' al with()
     */
    public function paginate(int $perPage = 10)
    {
        return Product::with([
            'images.slot', 
            'categories'   
        ])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }

    /**
     * Buscar por ID (Uso interno / Admin)
     */
    public function findById(int $id): ?Product
    {
        return Product::with([
            'images.slot',
            'categories',        
            'contentItems.slot', 
            'contentTexts.slot'
        ])->find($id);
    }

    /**
     * Buscar por SLUG (Para el Frontend Público)
     * Este método es vital para: /productos/laptop-gamer
     */
    public function findBySlug(string $slug): ?Product
    {
        return Product::with([
            'images.slot',       
            'categories',        
            'contentItems.slot', 
            'contentTexts.slot'
        ])->where('slug', $slug)->first();
    }

    /**
     * Actualizar datos básicos
     */
    public function update(int $id, array $data): bool
    {
        $product = $this->findById($id);
        return $product ? $product->update($data) : false;
    }

    /**
     * Eliminar producto
     */
    public function delete(int $id): bool
    {
        return Product::destroy($id) > 0;
    }
}