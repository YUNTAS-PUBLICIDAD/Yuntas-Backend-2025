<?php

namespace App\Application\Services\Product;

use App\Application\DTOs\Product\ProductDTO;
use App\Domain\Repositories\Product\ProductRepositoryInterface;
use App\Models\ImageSlot;
use App\Models\ProductContentSlot;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateProductService
{
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function execute(int $id, ProductDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $product = $this->repository->findById($id);
            if (!$product) {
                throw new ModelNotFoundException("Producto no encontrado");
            }

            $product->update([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'short_description' => $dto->short_description,
                'description' => $dto->description,
                'price' => $dto->price,
                // 'status' => $dto->status, // Opcional, si se actualiza aquí
            ]);

            if ($dto->main_image) {
                $oldMain = $product->images()->whereHas('slot', fn($q) => $q->where('name', 'Main'))->first();
                if ($oldMain) {
                    $path = str_replace('/storage/', '', $oldMain->url);
                    Storage::disk('public')->delete($path);
                    $oldMain->delete();
                }

                $path = $dto->main_image->store('products/' . $product->id, 'public');
                $mainSlot = ImageSlot::firstOrCreate(['name' => 'Main', 'module' => 'products']);
                $product->images()->create(['slot_id' => $mainSlot->id, 'url' => '/storage/' . $path]);
            }

            if (!empty($dto->gallery_images)) {
                $gallerySlot = ImageSlot::firstOrCreate(['name' => 'Gallery', 'module' => 'products']);
                foreach ($dto->gallery_images as $image) {
                    if (!$image instanceof \Illuminate\Http\UploadedFile) continue;
                    $path = $image->store('products/' . $product->id . '/gallery', 'public');
                    $product->images()->create(['slot_id' => $gallerySlot->id, 'url' => '/storage/' . $path]);
                }
            }

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
}