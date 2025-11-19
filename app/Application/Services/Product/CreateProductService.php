<?php

namespace App\Application\Services\Product;
use App\Application\DTOs\Product\ProductDTO;
use App\Domain\Repositories\Product\ProductRepositoryInterface;
use App\Models\ImageSlot;
use App\Models\ProductContentSlot;
use Illuminate\Support\Facades\DB;

class CreateProductService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(ProductDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            // 1. Guardar datos básicos
            $product = $this->repository->save([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'short_description' => $dto->short_description,
                'description' => $dto->description,
                'price' => $dto->price,
                'status' => $dto->status,
            ]);

            // 2. Guardar Imagen Principal (Lógica simplificada)
            if ($dto->main_image) {
                $path = $dto->main_image->store('products/' . $product->id, 'public');
                $mainSlot = ImageSlot::firstOrCreate(['name' => 'Main', 'module' => 'products']);
                
                $product->images()->create([
                    'slot_id' => $mainSlot->id,
                    'url' => '/storage/' . $path,
                ]);
            }

            // 3. Galería
            if (!empty($dto->gallery_images)) {
                $gallerySlot = ImageSlot::firstOrCreate(['name' => 'Gallery', 'module' => 'products']);
                foreach ($dto->gallery_images as $image) {
                    if(!$image instanceof \Illuminate\Http\UploadedFile) continue;
                    
                    $path = $image->store('products/' . $product->id . '/gallery', 'public');
                    $product->images()->create([
                        'slot_id' => $gallerySlot->id,
                        'url' => '/storage/' . $path,
                    ]);
                }
            }

            // 4. Especificaciones (Content Items)
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
}