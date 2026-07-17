<?php

namespace App\Application\Services\Blog;

use App\Application\DTOs\Blog\BlogDTO;
use App\Models\Blog;
use App\Domain\Repositories\Blog\BlogRepositoryInterface;
use Illuminate\Support\Str;
use App\Models\ImageSlot;
use App\Models\BlogContentSlot;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Traits\ValidatesImageSecurity;
use App\Traits\SanitizesInput;

class BlogService
{
    use ValidatesImageSecurity, SanitizesInput;

    public function __construct(
        private BlogRepositoryInterface $repository
    ) {}

    public function getAll(int $perPage = 10)
    {
        return $this->repository->paginate($perPage);
    }

    public function getDetail(string $slug)
    {
        $blog = $this->repository->findBySlug($slug);
        if (!$blog) throw new ModelNotFoundException("Artículo no encontrado");
        return $blog;
    }

    public function create(BlogDTO $dto)
    {
        $this->preValidateImages($dto);
        $dto = $this->sanitizeBlogInput($dto);

        return DB::transaction(function () use ($dto) {
            // 1. Crear Datos Básicos
            $blog = $this->repository->save([
                'title' => $dto->title,
                'slug' => $dto->slug,
                'hero_title' => $dto->hero_title,
                'cover_subtitle' => $dto->cover_subtitle,
                'status' => $dto->status,
                'video_url' => $dto->video_url,
                'video_description' => $dto->video_description,
                'video_subtitle' => $dto->video_subtitle,
                'keywords' => $dto->keywords,

                'product_id' => $dto->product_id,

                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
            ]);

            // 2. Guardar Items 
            if (!empty($dto->benefits)) {
                $this->saveContentItems($blog, 'Beneficios', $dto->benefits);
            }

            // 3. Guardar Texts 
            if (!empty($dto->description)) {
                $this->saveContentTexts($blog, 'Descripciones', $dto->description);
            }

            if (!empty($dto->testimonial)) {
                $this->saveContentTexts($blog, 'Testimonios', $dto->testimonial);
            }

            // 5. Imagen Principal
            if ($dto->main_image) {
                $title = $dto->main_image_title ?? $blog->title;
                $alt = $dto->main_image_alt ?? $blog->title;
                $this->uploadImage($blog, $dto->main_image, 'List', 'blogs', $title, $alt);
            }

            // 6. Gestionar Galería con Mapa de Slots
            if (!empty($dto->gallery)) {
                foreach ($dto->gallery as $item) {
                    $slotName = $item['slot'];
                    $image = $item['image'];
                    $title = $item['title'] ?? $blog->title;
                    $altText = $item['alt'] ?? $blog->title;

                    // Validar que sea archivo
                    if (!$image instanceof UploadedFile) continue;

                    $this->uploadImage($blog, $image, $slotName, 'blogs', $title, $altText);
                }
            }

            return $blog->refresh();
        });
    }

    public function update(int $id, BlogDTO $dto)
    {
        $this->preValidateImages($dto);
        $dto = $this->sanitizeBlogInput($dto);

        return DB::transaction(function () use ($id, $dto) {
            $blog = $this->repository->findById($id);
            if (!$blog) throw new ModelNotFoundException("Artículo de blog no encontrado");

            // 1. Actualizar Datos Básicos
            $blog->update([
                'title' => $dto->title,
                'slug' => $dto->slug,
                'hero_title' => $dto->hero_title,
                'cover_subtitle' => $dto->cover_subtitle,
                'status' => $dto->status,
                'video_url' => $dto->video_url,
                'video_description' => $dto->video_description,
                'video_subtitle' => $dto->video_subtitle,    
                'keywords' => $dto->keywords,            
                'product_id' => $dto->product_id, 
                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
            ]);

            // 2. Beneficios
            if (isset($dto->benefits)) {
                $this->saveContentItems($blog, 'Beneficios', $dto->benefits);
            }

            // Guardar Texts 
            if (!empty($dto->description)) {
                $this->saveContentTexts($blog, 'Descripciones', $dto->description);
            }

            if (!empty($dto->testimonial)) {
                $this->saveContentTexts($blog, 'Testimonios', $dto->testimonial);
            }

            // 3. Actualizar Imagen Principal
            if ($dto->main_image instanceof UploadedFile) {
                $this->deleteImagesBySlot($blog, 'List');
                $title = $dto->main_image_title ?? $blog->title;
                $alt = $dto->main_image_alt ?? $blog->title;
                $this->uploadImage($blog, $dto->main_image, 'List', 'blogs', $title, $alt);
            } else {
                $this->updateImageTitle($blog, 'List', $dto->main_image_title ?? $blog->title);
                $this->updateImageAlt($blog, 'List', $dto->main_image_alt ?? $blog->title);
            }

           // 4. Actualizar Galería
            if (!empty($dto->gallery)) {
                foreach ($dto->gallery as $item) {
                    $slotName = $item['slot'];
                    $image = $item['image'] ?? null;
                    $title = $item['title'] ?? $blog->title;
                    $altText = $item['alt'] ?? $blog->title;

                    if ($image instanceof UploadedFile) {
                        // Nueva imagen: borrar anterior y subir nueva
                        $uniqueSlots = ['Hero', 'Desc', 'Benefits', 'Testimonial'];
                        if (in_array($slotName, $uniqueSlots)) {
                            $this->deleteImagesBySlot($blog, $slotName);
                        }
                        $this->uploadImage($blog, $image, $slotName, 'blogs', $title, $altText);
                    } else {
                        $this->updateImageTitle($blog, $slotName, $title);
                        $this->updateImageAlt($blog, $slotName, $altText);
                    }
                }
            }

            // Actualizar Títulos de la Galería
            if (!empty($dto->gallery_title)) {
                foreach ($dto->gallery_title as $slot => $title) {
                    $this->updateImageTitle($blog, $slot, $title ?? $blog->title);
                }
            }
            
            // solo para actualizar ALT de la galería
            if (!empty($dto->gallery_alt)) {
                foreach ($dto->gallery_alt as $slot => $alt) {
                    $this->updateImageAlt($blog, $slot, $alt ?? $blog->title);
                }
            }

            return $blog->refresh();
        });
    }

    public function delete(int $id): void
    {
        $blog = $this->repository->findById($id);
        if (!$blog) throw new ModelNotFoundException("Blog no encontrado");
        
        $this->repository->delete($id);
    }

    private function uploadImage(Blog $blog, $file, $slotName, $module, $title, $altText = null)
    {
        $slotName = $this->validateSlot($slotName);
        $title = $this->sanitizeText($title);
        $altText = $this->sanitizeText($altText);

        // 1. Buscar o Crear el Slot
        $slot = ImageSlot::firstOrCreate(
            ['name' => $slotName, 'module' => $module]
        );

        // 2. Generar nombre basado en slug del blog
        $slugName = $blog->slug;
        $slotLower = strtolower($slotName);
        $extension = $file->extension();

        // Formato por ejemplo: proyector-holografico-3d-hero-15-a1b2c3.webp
        $uniqueSuffix = Str::lower(Str::random(6));
        $filename = "{$slugName}-{$slotLower}-{$blog->id}-{$uniqueSuffix}.{$extension}";

        // 3. Subir Archivo
        $path = $file->storeAs('blogs/' . $blog->id . '/' . $slotName, $filename, 'public');

        // 4. Crear Registro en DB
        $blog->images()->create([
            'slot_id' => $slot->id,
            'url' => '/storage/' . $path,
            'title' => $title,
            'alt_text' => $altText ?? $blog->title,
        ]);
    }

    private function updateImageTitle(Blog $blog, string $slotName, string $title): void
    {
        // Buscar el Slot
        $slot = ImageSlot::where(
            ['name' => $slotName, 'module' => 'blogs']
        )->first();
        if (!$slot) return;

        $image = $blog->images()->where('slot_id', $slot->id)->first();
        
        if ($image) {
            $image->update(['title' => $title]);
        }
    }

    private function updateImageAlt(Blog $blog, string $slotName, string $alt): void
    {   
        // Buscar el Slot
        $slot = ImageSlot::where(
            ['name' => $slotName, 'module' => 'blogs']
        )->first();

        if (!$slot) return;

        $image = $blog->images()->where('slot_id', $slot->id)->first();
        
        if ($image) {
            $image->update(['alt_text' => $alt]);
        }
    }

    private function deleteImagesBySlot(Blog $blog, $slotName)
    {
        $slot = ImageSlot::where(['name' => $slotName, 'module' => 'blogs'])->first();
        if (!$slot) return;

        $images = $blog->images()->where('slot_id', $slot->id)->get();
        foreach ($images as $img) {
            if (Storage::disk('public')->exists(str_replace('/storage/', '', $img->url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $img->url));
            }
            $img->delete();
        }
    }

    private function saveContentItems(Blog $blog, $slotName, array $items)
    {
        $slot = BlogContentSlot::firstOrCreate(['name' => $slotName]);

        $blog->contentItems()->where('slot_id', $slot->id)->delete();

        foreach ($items as $index => $itemData) {
            $text = is_string($index) ? "$index: $itemData" : $itemData;
            
            if(empty(trim($text))) continue;

            $blog->contentItems()->create([
                'slot_id' => $slot->id,
                'text' => $text
            ]);
        }
    }

    private function saveContentTexts(Blog $blog, $slotName, string $content)
    {
        $slot = BlogContentSlot::firstOrCreate(['name' => $slotName]);

        $blog->contentTexts()->where('slot_id', $slot->id)->delete();

        if (!empty(trim($content))) {
            $blog->contentTexts()->create([
                'slot_id' => $slot->id,
                'content' => $content
            ]);
        }
    }

    /**
     * Pre-validación de seguridad de imágenes antes del procesamiento
     */
    private function preValidateImages(BlogDTO $dto): void
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

    /**
     * Sanitización completa de todos los campos del BlogDTO
     */
    private function sanitizeBlogInput(BlogDTO $dto): BlogDTO
    {
        $dto->title = $this->sanitizeText($dto->title);
        $dto->slug = $this->sanitizeSlug($dto->slug);
        $dto->hero_title = $this->sanitizeText($dto->hero_title);
        $dto->cover_subtitle = $this->sanitizeText($dto->cover_subtitle);
        $dto->meta_title = $this->sanitizeText($dto->meta_title);
        $dto->meta_description = $this->sanitizeText($dto->meta_description);
        $dto->keywords = $this->sanitizeArray($dto->keywords);
        $dto->description = $this->sanitizeHtml($dto->description);
        $dto->testimonial = $this->sanitizeHtml($dto->testimonial);
        $dto->benefits = $this->sanitizeArray($dto->benefits);
        
        if ($dto->video_url) {
            $dto->video_url = $this->sanitizeUrl($dto->video_url);
        }
        $dto->video_description = $this->sanitizeHtml($dto->video_description);
        $dto->video_subtitle = $this->sanitizeText($dto->video_subtitle);

        $dto->product_id = $this->sanitizeInteger($dto->product_id);

        return $dto;
    }

    // Validación específica para slots de blog
    private function validateSlot(string $slotName): string
    {
        $allowedSlots = ['List', 'Hero', 'Desc', 'Benefits', 'Testimonial'];
        return $this->validateWhitelist($slotName, $allowedSlots, 'slot');
    }
}