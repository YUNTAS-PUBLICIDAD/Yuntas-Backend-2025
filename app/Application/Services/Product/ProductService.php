<?php

namespace App\Application\Services\Product;

use App\Application\DTOs\Product\ProductDTO;
use App\Models\Product;
use App\Models\ImageSlot;
use App\Models\ProductContentSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    public function __construct(
        // Assuming you might want to keep the repository pattern, 
        // but the code provided uses Eloquent directly for simplicity.
        // If you strictly use repo, inject it here.
    ) {}

    public function getAll(int $perPage = 10)
    {
        return Product::paginate($perPage);
    }

    public function getDetail(string $term)
    {
        $product = null;

        if (is_numeric($term)) {
            $product = Product::find((int)$term);
        }

        if (!$product) {
            $product = Product::where('slug', $term)->first();
        }
        if (!$product) {
            throw new ModelNotFoundException("Producto no encontrado con el término: $term");
        }

        return $product;
    }
    // Crear Producto
    public function create(ProductDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            // 1. Guardar datos básicos
            $product = Product::create([
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

            // 2. Sincronizar Categorías (Vital)
            if (!empty($dto->categories)) {
                $categoryIds = $this->resolveCategoryIds($dto->categories);
                $product->categories()->sync($categoryIds);
            }

            // 3. Gestionar Imagen Principal (Slot: 'List')
            if ($dto->main_image) {
                // Tu código usaba 'List', el nuevo usaba 'Main'. 
                $alt = $dto->main_image_alt ?? $product->name;
                $this->uploadImage($product, $dto->main_image, 'List', 'products', $alt);
            }

            // 4. Gestionar Galería con Mapa de Slots (Tu lógica específica)
            $slotMap = [0 => 'Hero', 1 => 'Specs', 2 => 'Benefits', 3 => 'Popups'];
            
            if (!empty($dto->gallery_images)) {
                foreach ($dto->gallery_images as $index => $image) {
                    // Validar que sea archivo
                    if (!$image instanceof \Illuminate\Http\UploadedFile) continue;

                    // Determinar Slot y Alt Text
                    $slotName = $slotMap[$index] ?? 'Gallery';
                    $altText = $dto->gallery_alts[$index] ?? $product->name;

                    $this->uploadImage($product, $image, $slotName, 'products', $altText);
                }
            }

            // 5. Guardar Items (Especificaciones)
            if (!empty($dto->specifications)) {
                $this->saveContentItems($product, 'Especificaciones', $dto->specifications);
            }

            // 6. Guardar Items (Beneficios)
            if (!empty($dto->benefits)) {
                $this->saveContentItems($product, 'Beneficios', $dto->benefits);
            }

            return $product->load('images', 'categories', 'contentItems');
        });
    }

    // Actualizar Producto
    public function update(int $id, ProductDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $product = Product::findOrFail($id);

            $product->update([
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

           if (!empty($dto->categories)) {
                $nombresCategorias = is_array($dto->categories) 
                    ? $dto->categories 
                    : [$dto->categories];

                $categoryIds = $this->resolveCategoryIds($nombresCategorias);
                $product->categories()->sync($categoryIds);
            }

            // Actualizar Imagen Principal
            if ($dto->main_image) {
                $this->deleteImagesBySlot($product, 'List');
                $alt = $dto->main_image_alt ?? $product->name;
                $this->uploadImage($product, $dto->main_image, 'List', 'products', $alt);
            }

            // Actualizar Galería
            $slotMap = [0 => 'Hero', 1 => 'Specs', 2 => 'Benefits', 3 => 'Popups'];

            if (!empty($dto->gallery_images)) {
                foreach ($dto->gallery_images as $index => $image) {
                    if (!$image instanceof \Illuminate\Http\UploadedFile) continue;

                    $slotName = $slotMap[$index] ?? 'Gallery';
                    $altText = $dto->gallery_alts[$index] ?? $product->name;

                    // Si es uno de los slots únicos (Hero, Specs...), borramos el anterior para reemplazarlo
                    if (in_array($slotName, ['Hero', 'Specs', 'Benefits', 'Popups'])) {
                        $this->deleteImagesBySlot($product, $slotName);
                    }

                    $this->uploadImage($product, $image, $slotName, 'products', $altText);
                }
            }

            // Actualizar Contenido
            if (isset($dto->specifications)) {
                $this->saveContentItems($product, 'Especificaciones', $dto->specifications);
            }
            if (isset($dto->benefits)) {
                $this->saveContentItems($product, 'Beneficios', $dto->benefits);
            }

            return $product->refresh();
        });
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }


    private function uploadImage(Product $product, $file, $slotName, $module, $altText = null)
    {
        // 1. Buscar o Crear el Slot
        $slot = ImageSlot::firstOrCreate(
            ['name' => $slotName, 'module' => $module]
        );

        // 2. Subir Archivo
        $path = $file->store('products/' . $product->id . '/' . strtolower($slotName), 'public');

        // 3. Crear Registro en DB
        $product->images()->create([
            'slot_id' => $slot->id,
            'url' => '/storage/' . $path,
            'title' => $product->name, 
            'alt_text' => $altText ?? $product->name,
        ]);
    }

    private function deleteImagesBySlot(Product $product, $slotName)
    {
        $slot = ImageSlot::where('name', $slotName)->first();
        if (!$slot) return;

        $images = $product->images()->where('slot_id', $slot->id)->get();
        foreach ($images as $img) {
            if (Storage::disk('public')->exists(str_replace('/storage/', '', $img->url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $img->url));
            }
            $img->delete();
        }
    }

    private function saveContentItems(Product $product, $slotName, array $items)
    {
        $slot = ProductContentSlot::firstOrCreate(
            ['name' => $slotName],
            ['data_type' => 'list', 'position' => 1]
        );

        $product->contentItems()->where('slot_id', $slot->id)->delete();

        foreach ($items as $index => $itemData) {
            $text = is_string($index) ? "$index: $itemData" : $itemData;
            
            if(empty(trim($text))) continue;

            $product->contentItems()->create([
                'slot_id' => $slot->id,
                'text' => $text,
                'position' => $index + 1
            ]);
        }
    }
    /**
     * Recibe un array de NOMBRES de categorías (strings).
     * Busca el ID si existe, o crea la categoría si es nueva.
     * Retorna un array de IDs para sincronizar.
     */
    private function resolveCategoryIds(array $categoryNames): array
    {
        $ids = [];
        foreach ($categoryNames as $name) {
            if (empty(trim($name))) continue;

            // Buscamos por nombre o creamos nueva
            $category = \App\Models\Category::firstOrCreate(
                ['name' => trim($name)], // Buscamos por nombre exacto
                ['slug' => \Illuminate\Support\Str::slug($name)] // Si se crea, generamos slug
            );
            
            $ids[] = $category->id;
        }
        return $ids;
    }
}