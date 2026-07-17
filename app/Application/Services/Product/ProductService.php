<?php

namespace App\Application\Services\Product;

use App\Application\DTOs\Product\ProductDTO;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ImageSlot;
use App\Models\ProductContentSlot;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Traits\ValidatesImageSecurity;
use App\Traits\SanitizesInput;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    use ValidatesImageSecurity, SanitizesInput;

    public function __construct() {}

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
        $product->load([
            'images.slot',
            'contentItems.slot',
            'categories'
        ]);
        return $product;
    }
    // Crear Producto
    public function create(ProductDTO $dto)
    {
        $this->preValidateImages($dto);
        $dto = $this->sanitizeProductInput($dto);

        $product = DB::transaction(function () use ($dto) {
            // 1. Guardar datos básicos
            $product = Product::create([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'hero_title' => $dto->hero_title,
                'description' => $dto->description,
                'price' => $dto->price,
                'status' => $dto->status,
                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
                'keywords' => $dto->keywords,
                'video_url' => $dto->video_url,
                'video_description' => $dto->video_description,
                'video_subtitle' => $dto->video_subtitle,
            ]);

            // 2. Sincronizar Categorías (Vital)
            if (!empty($dto->categories)) {
                $categoryIds = $this->resolveCategoryIds($dto->categories);
                $product->categories()->sync($categoryIds);
            }

            // 3. Gestionar Imagen Principal (Slot: 'List')
            if ($dto->main_image) {
                $title = $dto->main_image_title ?? $product->name;
                $alt = $dto->main_image_alt ?? $product->name;
                $this->uploadImage($product, $dto->main_image, 'List', 'products', $title, $alt);
            }

            // 4. Gestionar Galería con Mapa de Slots
            if (!empty($dto->gallery)) {
                foreach ($dto->gallery as $item) {
                    $slotName = $item['slot'];
                    $image = $item['image'];
                    $title = $item['title'] ?? $product->name;
                    $altText = $item['alt'] ?? $product->name;

                    // Validar que sea archivo
                    if (!$image instanceof UploadedFile) continue;

                    $this->uploadImage($product, $image, $slotName, 'products', $title, $altText);
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

        return $product;
    }

    // Actualizar Producto
    public function update(int $id, ProductDTO $dto)
    {
        $this->preValidateImages($dto);
        $dto = $this->sanitizeProductInput($dto);

        $product = DB::transaction(function () use ($id, $dto) {
            $product = Product::findOrFail($id);

            $product->update([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'hero_title' => $dto->hero_title,
                'description' => $dto->description,
                'price' => $dto->price,
                'status' => $dto->status,
                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
                'keywords' => $dto->keywords,
                'video_url' => $dto->video_url,
                'video_description' => $dto->video_description,
                'video_subtitle' => $dto->video_subtitle,
            ]);

           if (!empty($dto->categories)) {
                $nombresCategorias = is_array($dto->categories)
                    ? $dto->categories
                    : [$dto->categories];

                $categoryIds = $this->resolveCategoryIds($nombresCategorias);
                $product->categories()->sync($categoryIds);
            }

            // Actualizar Imagen Principal
            if ($dto->main_image instanceof UploadedFile) {
                $this->deleteImagesBySlot($product, 'List');
                $title = $dto->main_image_title ?? $product->name;
                $alt = $dto->main_image_alt ?? $product->name;
                $this->uploadImage($product, $dto->main_image, 'List', 'products', $title, $alt);
            } else {
                $this->updateImageTitle($product, 'List', $dto->main_image_title ?? $product->name);
                $this->updateImageAlt($product, 'List', $dto->main_image_alt ?? $product->name);
            }

            // Actualizar Galería
            if (!empty($dto->gallery)) {
                foreach ($dto->gallery as $item) {
                    $slotName = $item['slot'];
                    $image = $item['image'] ?? null;
                    $title = $item['title'] ?? $product->name;
                    $altText = $item['alt'] ?? $product->name;

                    if ($image instanceof UploadedFile) {
                        // Nueva imagen: borrar anterior y subir nueva
                        $uniqueSlots = ['Hero', 'Specs', 'Benefits', 'PopupLeft', 'PopupRight', 'PopupMobile'];
                        if (in_array($slotName, $uniqueSlots)) {
                            $this->deleteImagesBySlot($product, $slotName);
                        }
                        $this->uploadImage($product, $image, $slotName, 'products', $title, $altText);
                    } else {
                        $this->updateImageTitle($product, $slotName, $title);
                        $this->updateImageAlt($product, $slotName, $altText);
                    }
                }
            }

            // Actualizar Títulos de la Galería
            if (!empty($dto->gallery_title)) {
                foreach ($dto->gallery_title as $slot => $title) {
                    $this->updateImageTitle($product, $slot, $title ?? $product->name);
                }
            }

            // solo para actualizar ALT de la galería
            if (!empty($dto->gallery_alt)) {
                foreach ($dto->gallery_alt as $slot => $alt) {
                    $this->updateImageAlt($product, $slot, $alt ?? $product->name);
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

        return $product;
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);
            $product->delete();
        });

    }


    private function uploadImage(Product $product, $file, $slotName, $module, $title, $altText = null)
    {
        $slotName = $this->validateSlot($slotName);
        $title = $this->sanitizeText($title);
        $altText = $this->sanitizeText($altText);

        // 1. Buscar o Crear el Slot
        $slot = ImageSlot::firstOrCreate(
            ['name' => $slotName, 'module' => $module]
        );

        // 2. Generar nombre basado en slug del producto
        $slugName = Str::slug($product->name);
        $slotLower = strtolower($slotName);
        $extension = $file->extension();

        // Formato por ejemplo: proyector-holografico-3d-hero-15-a1b2c3.webp
        $uniqueSuffix = Str::lower(Str::random(6));
        $filename = "{$slugName}-{$slotLower}-{$product->id}-{$uniqueSuffix}.{$extension}";

        // 3. Subir Archivo
        $path = $file->storeAs('products/' . $product->id . '/' . $slotName, $filename, 'public');

        // 4. Crear Registro en DB
        $product->images()->create([
            'slot_id' => $slot->id,
            'url' => '/storage/' . $path,
            'title' => $title,
            'alt_text' => $altText ?? $product->name,
        ]);
    }

    private function updateImageTitle(Product $product, string $slotName, string $title): void
    {
        // Buscar el Slot
        $slot = ImageSlot::where(
            ['name' => $slotName, 'module' => 'products']
        )->first();
        if (!$slot) return;

        $image = $product->images()->where('slot_id', $slot->id)->first();

        if ($image) {
            $image->update(['title' => $title]);
        }
    }

    private function updateImageAlt(Product $product, string $slotName, string $alt): void
    {
        // Buscar el Slot
        $slot = ImageSlot::where(
            ['name' => $slotName, 'module' => 'products']
        )->first();

        if (!$slot) return;

        $image = $product->images()->where('slot_id', $slot->id)->first();

        if ($image) {
            $image->update(['alt_text' => $alt]);
        }
    }

    private function deleteImagesBySlot(Product $product, $slotName)
    {
        $slot = ImageSlot::where(['name' => $slotName, 'module' => 'products'])->first();
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
        $slot = ProductContentSlot::firstOrCreate(['name' => $slotName]);

        $product->contentItems()->where('slot_id', $slot->id)->delete();

        foreach ($items as $index => $itemData) {
            $text = is_string($index) ? "$index: $itemData" : $itemData;

            if(empty(trim($text))) continue;

            $product->contentItems()->create([
                'slot_id' => $slot->id,
                'text' => $text
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

    private function preValidateImages(ProductDTO $dto): void
    {
        // Validar imagen principal
        if ($dto->main_image instanceof UploadedFile) {
            $this->validateImageSecurity($dto->main_image);
        }

        // Validar imágenes de galería
        if (!empty($dto->gallery)) {
            foreach ($dto->gallery as $item) {
                if (isset($item['image']) && $item['image'] instanceof UploadedFile) {
                    $this->validateImageSecurity($item['image']);
                }
            }
        }
    }

    // Sanitización de Inputs específicos para Producto
    private function sanitizeProductInput(ProductDTO $dto): ProductDTO
    {
        // Sanitizar campos de texto
        $dto->name = $this->sanitizeText($dto->name);
        $dto->hero_title = $this->sanitizeText($dto->hero_title);
        $dto->description = $this->sanitizeHtml($dto->description);
        $dto->meta_title = $this->sanitizeText($dto->meta_title);
        $dto->meta_description = $this->sanitizeText($dto->meta_description);
        $dto->keywords = $this->sanitizeKeywords($dto->keywords);
        $dto->slug = $this->sanitizeSlug($dto->slug);
        $dto->price = $this->sanitizeFloat($dto->price);
        $dto->video_description = $this->sanitizeHtml($dto->video_description);
        $dto->video_subtitle = $this->sanitizeText($dto->video_subtitle);

        if ($dto->video_url) {
            $dto->video_url = $this->sanitizeUrl($dto->video_url);
        }

        // Sanitizar arrays
        if ($dto->specifications) {
            $dto->specifications = $this->sanitizeArray($dto->specifications);
        }
        if ($dto->benefits) {
            $dto->benefits = $this->sanitizeArray($dto->benefits);
        }
        if ($dto->categories) {
            $dto->categories = $this->sanitizeArray($dto->categories);
        }

        return $dto;
    }

    // Validación específica para slots de productos
    private function validateSlot(string $slotName): string
    {
        $allowedSlots = ['List', 'Hero', 'Specs', 'Benefits', 'PopupLeft', 'PopupRight', 'PopupRight', 'PopupMobile'];
        return $this->validateWhitelist($slotName, $allowedSlots, 'slot');
    }

    // public function searchForChatbot(string $query)
    // {
    //   $words = collect(explode(' ', strtolower($query)))
    //           ->filter(fn($w) => strlen($w) > 2) // quitar ruido tipo "de", "el"
    //           ->values();

    //       $products = Product::query()
    //           ->with(['images.slot'])
    //           ->where(function ($q) use ($words) {
    //               foreach ($words as $word) {
    //                   $q->orWhere('name', 'like', "%{$word}%");
    //               }
    //           })
    //           ->limit(3)
    //           ->get();

    //       return $products->map(function ($product) {
    //           $image = $product->images->firstWhere('slot.name', 'List');

    //           return [
    //               'id' => $product->id,
    //               'name' => $product->name,
    //               'slug' => $product->slug,
    //               'price' => $product->price,
    //               'image' => $image?->url,
    //           ];
    //       });
    // }
    //

    public function searchForChatbot(string $query)
    {
      $cacheKey = 'chatbot_search_' . md5($query);

      return Cache::remember($cacheKey, 60, function () use ($query) {

             $query = $this->normalizeText(trim($query));

             $words = collect(explode(' ', $query))
                 ->filter(fn ($w) => strlen($w) > 2)
                 ->values();

             if ($words->isEmpty()) {
                 return collect();
             }

             $products = Product::query()
                 ->with(['images.slot'])
                 ->get()
                 ->map(function ($product) use ($query, $words) {

                     $name = $this->normalizeText($product->name);

                     $score = 0;

                     // Match exacto completo
                     if ($name === $query) {
                         $score += 100;
                     }

                     // Contiene frase completa
                     if (str_contains($name, $query)) {
                         $score += 50;
                     }

                     // Match por palabras ponderado
                     foreach ($words as $word) {

                         // Exact match palabra
                         if ($name === $word) {
                             $score += 80;
                             continue;
                         }

                         // Empieza por
                         if (str_starts_with($name, $word)) {
                             $score += 30;
                         }

                         // Contiene
                         if (str_contains($name, $word)) {
                             $score += 10;
                         }
                     }

                     return [
                         'score' => $score,
                         'product' => $product
                     ];
                 })

                 // Solo relevantes
                 ->filter(fn ($item) => $item['score'] > 0)

                 // Mejor score primero
                 ->sortByDesc('score')

                 // Solo el mejor
                 ->take(1);

             return $products->map(function ($item) {

                 $product = $item['product'];

                 $image = $product->images
                     ->firstWhere('slot.name', 'List');

                 return [
                     'id' => $product->id,
                     'name' => $product->name,
                     'slug' => $product->slug,
                     'price' => $product->price,
                     'image' => $image?->url
                 ];
             })->values();
         });
    }

    private function normalizeText(string $text): string
    {
      $text = mb_strtolower($text);

      $replace = [
             'á' => 'a',
             'é' => 'e',
             'í' => 'i',
             'ó' => 'o',
             'ú' => 'u',
             'ñ' => 'n',
         ];

         return strtr($text, $replace);
    }

    public function getFeaturedForChatbot()
    {
      return Cache::remember('chatbot_featured_products', 60, function () {
      return Product::query()
        ->with(['images.slot'])
        ->orderByDesc('updated_at')
        ->limit(3)
        ->get()
        ->map(function ($product) {
          $image = $product->images->firstWhere('slot.name', 'List' );

          return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'image' => $image?->url
          ];
        });
      });
    }
}
