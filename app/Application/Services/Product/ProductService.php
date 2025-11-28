<?php

namespace App\Application\Services\Product;

use App\Application\DTOs\Product\ProductDTO;
use App\Domain\Repositories\Product\ProductRepositoryInterface;
use App\Models\ImageSlot;
use App\Models\ProductContentSlot;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    /**
     * Listar todos los productos (Catálogo)
     */
    public function getAll(int $perPage = 10)
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Obtener un producto por Slug o ID
     */
    public function getDetail(string $slug)
    {
        $product = $this->repository->findBySlug($slug);

        if (!$product) {
            throw new ModelNotFoundException("Producto no encontrado con slug: $slug");
        }

        return $product;
    }

    /**
     * Crear un producto nuevo
     */
    public function create(ProductDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            // 1. Guardar Producto
            $product = $this->repository->save([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'short_description' => $dto->short_description,
                'description' => $dto->description,
                'price' => $dto->price,
                'status' => $dto->status,
                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
                'keywords' => $dto->keywords,
            ]);

            // 2. Imagen Principal
            if ($dto->main_image) {
                $path = $dto->main_image->store('products/' . $product->id, 'public');
                $mainSlot = ImageSlot::firstOrCreate(['name' => 'Main', 'module' => 'products']);
                
                $product->images()->create([
                    'slot_id' => $mainSlot->id,
                    'url' => '/storage/' . $path,
                    'title' => $product->name,
                    'alt_text' => 'Imagen principal de ' . $product->name,
                ]);
            }

            // 3. Galería
            if (!empty($dto->gallery_images)) {
                $gallerySlot = ImageSlot::firstOrCreate(['name' => 'Gallery', 'module' => 'products']);
                foreach ($dto->gallery_images as $image) {
                    if (!$image instanceof \Illuminate\Http\UploadedFile) continue;
                    $path = $image->store('products/' . $product->id . '/gallery', 'public');
                    $product->images()->create([
                        'slot_id' => $gallerySlot->id,
                        'url' => '/storage/' . $path,
                    ]);
                }
            }

            // 4. Especificaciones
            if (!empty($dto->specifications)) {
                $specSlot = ProductContentSlot::firstOrCreate(['name' => 'Especificaciones', 'data_type' => 'list']);
                foreach ($dto->specifications as $key => $value) {
                    $text = is_string($key) ? "$key: $value" : $value;
                    $product->contentItems()->create([
                        'slot_id' => $specSlot->id,
                        'text' => $text,
                        'position' => 0 
                    ]);
                }
            }

            return $product;
        });
    }

    /**
     * Actualizar un producto existente
     */
    public function update(int $id, ProductDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $product = $this->repository->findById($id);
            if (!$product) throw new ModelNotFoundException("Producto no encontrado");

            // Actualizar básicos
            $product->update([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'short_description' => $dto->short_description,
                'description' => $dto->description,
                'price' => $dto->price,
                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
                'keywords' => $dto->keywords,
            ]);

            // Actualizar Imagen Principal (Reemplazo)
            if ($dto->main_image) {
                $oldMain = $product->images()->whereHas('slot', fn($q) => $q->where('name', 'Main'))->first();
                if ($oldMain) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $oldMain->url));
                    $oldMain->delete();
                }

                $path = $dto->main_image->store('products/' . $product->id, 'public');
                $mainSlot = ImageSlot::firstOrCreate(['name' => 'Main', 'module' => 'products']);
                $product->images()->create([
                    'slot_id' => $mainSlot->id, 
                    'url' => '/storage/' . $path,
                    'title' => $product->name,
                    'alt_text' => 'Imagen actualizada de ' . $product->name
                ]);
            }

            // Actualizar Galería (Agregar nuevas)
            if (!empty($dto->gallery_images)) {
                $gallerySlot = ImageSlot::firstOrCreate(['name' => 'Gallery', 'module' => 'products']);
                foreach ($dto->gallery_images as $image) {
                    if (!$image instanceof \Illuminate\Http\UploadedFile) continue;
                    $path = $image->store('products/' . $product->id . '/gallery', 'public');
                    $product->images()->create(['slot_id' => $gallerySlot->id, 'url' => '/storage/' . $path]);
                }
            }

            // Actualizar Especificaciones (Borrar y Recrear)
            if (!empty($dto->specifications)) {
                $specSlot = ProductContentSlot::firstOrCreate(['name' => 'Especificaciones', 'data_type' => 'list']);
                $product->contentItems()->where('slot_id', $specSlot->id)->delete();
                
                foreach ($dto->specifications as $key => $value) {
                    $text = is_string($key) ? "$key: $value" : $value;
                    $product->contentItems()->create(['slot_id' => $specSlot->id, 'text' => $text, 'position' => 0]);
                }
            }

            return $product->refresh();
        });
    }

    /**
     * Eliminar un producto
     */
    public function delete(int $id): void
    {
        $product = $this->repository->findById($id);
        if (!$product) throw new ModelNotFoundException("Producto no encontrado");
        
        $this->repository->delete($id);
    }
}