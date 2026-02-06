<?php

namespace App\Application\Services\Product;

use App\Application\DTOs\Product\ProductDTO;
use App\Models\Product;
use App\Models\ImageSlot;
use App\Models\ProductContentSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
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
            //$slotMap = [0 => 'Hero', 1 => 'Specs', 2 => 'Benefits', 3 => 'Popups'];
            
            if (!empty($dto->gallery)) {
                foreach ($dto->gallery as $item) {
                    $slotName = $item['slot'];
                    $image = $item['image'];
                    $title = $item['title'] ?? $product->name;
                    $altText = $item['alt'] ?? $product->name;

                    // Validar que sea archivo
                    if (!$image instanceof \Illuminate\Http\UploadedFile) continue;

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

        if (app()->environment('production')) {
            $this->triggerFrontendRebuild();
        }

        return $product;
    }

    // Actualizar Producto
    public function update(int $id, ProductDTO $dto)
    {
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
            ]);

           if (!empty($dto->categories)) {
                $nombresCategorias = is_array($dto->categories) 
                    ? $dto->categories 
                    : [$dto->categories];

                $categoryIds = $this->resolveCategoryIds($nombresCategorias);
                $product->categories()->sync($categoryIds);
            }

            // Actualizar Imagen Principal
            if ($dto->main_image instanceof \Illuminate\Http\UploadedFile) {
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

                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        // Nueva imagen: borrar anterior y subir nueva
                        $uniqueSlots = ['Hero', 'Specs', 'Benefits', 'Popups'];
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

        if (app()->environment('production')) {
            $this->triggerFrontendRebuild();
        }

        return $product;
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);
            $product->delete();
        });

        if (app()->environment('production')) {
            $this->triggerFrontendRebuild();
        }
    }


    private function uploadImage(Product $product, $file, $slotName, $module, $title, $altText = null)
    {
        // 1. Buscar o Crear el Slot
        $slot = ImageSlot::firstOrCreate(
            ['name' => $slotName, 'module' => $module]
        );

        // 2. Generar nombre basado en slug del producto
        $slugName = Str::slug($product->name);
        $slotLower = strtolower($slotName);
        $extension = $file->extension();

        // Formato por ejemplo: proyector-holografico-3d-hero-15.webp
        $filename = "{$slugName}-{$slotLower}-{$product->id}.{$extension}";

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

        $image = $product->images()->where('slot_id', $slot->id)->first();
        
        if ($image) {
            $image->update(['alt_text' => $alt]);
        }
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


    /**
     * Trigger GitHub Action for Frontend Rebuild
     */
    private function triggerFrontendRebuild(): void
    {
        try {
            $token = $this->getGitHubAppToken();
            $repo = env('GITHUB_REPO');

            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github+json',
                'Authorization' => "Bearer {$token}",
                'X-GitHub-Api-Version' => '2022-11-28',
            ])->post("https://api.github.com/repos/{$repo}/dispatches", [
                'event_type' => 'rebuild-frontend',
                'client_payload' => [
                    'triggered_by' => 'product_update',
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            if ($response->successful()) {
                Log::info('Rebuild del frontend activado correctamente');
            } else {
                Log::warning('Respuesta del webhook de GitHub: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Error al activar el rebuild del frontend: ' . $e->getMessage());
        }
    }

    private function getGitHubAppToken(): string
    {
        $appId = env('GITHUB_APP_ID');
        $installationId = env('GITHUB_APP_INSTALLATION_ID');

        if (!$appId || !$installationId) {
            throw new \Exception('Credenciales GITHUB_APP_ID o GITHUB_APP_INSTALLATION_ID no configuradas');
        }
        
        $privateKey = null;

        // Intentar cargar desde archivo
        $privateKeyPath = env('GITHUB_APP_PRIVATE_KEY_PATH');
        
        if ($privateKeyPath) {
            if (strpos($privateKeyPath, '/') !== 0) {
                $privateKeyPath = base_path($privateKeyPath);
            }
            
            if (file_exists($privateKeyPath)) {
                $privateKey = file_get_contents($privateKeyPath);
            }
        }
        
        if (!$privateKey) { // Intentar cargar desde variable de entorno
            $privateKey = env('GITHUB_APP_PRIVATE_KEY');
        }
        
        if (!$privateKey) {
            Log::error('Clave privada de GitHub App no encontrada', [
                'path' => $privateKeyPath,
                'exists' => file_exists($privateKeyPath ?? ''),
                'base_path' => base_path()
            ]);
            throw new \Exception('Clave privada de GitHub App no encontrada');
        }

        $jwt = $this->generateJWT($privateKey, $appId);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$jwt}",
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->post("https://api.github.com/app/installations/{$installationId}/access_tokens");

        if (!$response->successful()) {
            throw new \Exception('No se pudo obtener el token de la GitHub App: ' . $response->body());
        }

        return $response->json()['token'];
    }

    // NOTA: no usamos firebase/php-jwt porque da muchos errores al instalarlo en nuestro caso, asi que lo hacemos manualmente
    private function generateJWT(string $privateKey, int $appId): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
        $payload = json_encode([
            'iat' => time(),
            'exp' => time() + 600,
            'iss' => $appId,
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = '';
        $success = openssl_sign(
            $base64UrlHeader . "." . $base64UrlPayload,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        if (!$success) {
            throw new \Exception('Error al firmar el token JWT');
        }

        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    private function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}