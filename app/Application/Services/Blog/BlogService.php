<?php

namespace App\Application\Services\Blog;

use App\Application\DTOs\Blog\BlogDTO;
use App\Domain\Repositories\Blog\BlogRepositoryInterface;
use App\Models\ImageSlot;
use App\Models\BlogContentSlot;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class BlogService
{
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
        return DB::transaction(function () use ($dto) {
            // 1. Crear Datos Básicos
            $blog = $this->repository->save([
                'title' => $dto->title,
                'slug' => $dto->slug,
                'cover_subtitle' => $dto->cover_subtitle,
                'content' => $dto->content,
                'status' => $dto->status,
                'video_url' => $dto->video_url,

                //  AQUÍ
                'product_id' => $dto->product_id,

                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
            ]);

            // 2. Sincronizar Categorías
            if (!empty($dto->categories)) {
                $blog->categories()->sync($dto->categories);
            }

            // 3. Imagen Principal
            if ($dto->main_image) {
                $this->saveMainImage($blog, $dto->main_image, $dto->main_image_alt);
            }

            // 4. Galería
            if (!empty($dto->gallery_images)) {
                $this->saveGalleryImages($blog, $dto->gallery_images, $dto->gallery_alts);
            }

            // 5. Contenido Dinámico 
            $this->saveContent($blog, $dto);

            return $blog->refresh();
        });
    }

    public function update(int $id, BlogDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $blog = $this->repository->findById($id);
            if (!$blog) throw new ModelNotFoundException("Artículo de blog no encontrado");

            // 1. Actualizar Datos Básicos
            $blog->update([
                'title' => $dto->title,
                'slug' => $dto->slug,
                'cover_subtitle' => $dto->cover_subtitle,
                'content' => $dto->content,
                'status' => $dto->status,
                'video_url' => $dto->video_url,                
                'product_id' => $dto->product_id, 
                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
            ]);

            // 2. Sincronizar Categorías
            if (isset($dto->categories)) {
                $blog->categories()->sync($dto->categories);
            }

            // 3. Imagen Principal 
            if ($dto->main_image) {
                $this->saveMainImage($blog, $dto->main_image, $dto->main_image_alt);
            }

            // 4. Galería 
            if (!empty($dto->gallery_images)) {
                $this->saveGalleryImages($blog, $dto->gallery_images, $dto->gallery_alts);
            }

            // 5. Contenido Dinámico 
            $this->saveContent($blog, $dto);

            return $blog->refresh();
        });
    }

    public function delete(int $id): void
    {
        $blog = $this->repository->findById($id);
        if (!$blog) throw new ModelNotFoundException("Blog no encontrado");
        
        foreach ($blog->images as $img) {
            if (Storage::disk('public')->exists(str_replace('/storage/', '', $img->url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $img->url));
            }
        }
        
        $this->repository->delete($id);
    }

    // --- Helpers Privados para Limpieza de Código ---

    private function saveMainImage($blog, UploadedFile $image, ?string $altText)
    {
        $mainSlot = ImageSlot::firstOrCreate(['name' => 'Main', 'module' => 'blogs']);
        
        // Borrar anterior
        $oldImage = $blog->images()->where('slot_id', $mainSlot->id)->first();
        if ($oldImage) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $oldImage->url));
            $oldImage->delete();
        }

        $path = $image->store('blogs/' . $blog->id, 'public');
        
        $blog->images()->create([
            'slot_id' => $mainSlot->id,
            'url' => '/storage/' . $path,
            'title' => $blog->title,
            'alt_text' => $altText ?? $blog->title 
        ]);
    }

    private function saveGalleryImages($blog, array $images, array $alts)
    {
        $gallerySlot = ImageSlot::firstOrCreate(['name' => 'Gallery', 'module' => 'blogs']);
        
        foreach ($images as $index => $img) {
            if (!$img instanceof UploadedFile) continue;
            
            $path = $img->store('blogs/' . $blog->id . '/gallery', 'public');
            $altText = $alts[$index] ?? $blog->title; 

            $blog->images()->create([
                'slot_id' => $gallerySlot->id, 
                'url' => '/storage/' . $path,
                'alt_text' => $altText
            ]);
        }
    }

    private function saveContent($blog, BlogDTO $dto) 
    {
        // Párrafos
        if (!empty($dto->paragraphs)) {
            $textSlot = BlogContentSlot::firstOrCreate(['name' => 'Parrafos', 'data_type' => 'text']);
            $blog->contentTexts()->where('slot_id', $textSlot->id)->delete();
            
            foreach ($dto->paragraphs as $text) {
                if(empty(trim($text))) continue;
                $blog->contentTexts()->create(['slot_id' => $textSlot->id, 'content' => $text]);
            }
        }

        // Beneficios
        if (!empty($dto->benefits)) {
            $listSlot = BlogContentSlot::firstOrCreate(['name' => 'Beneficios', 'data_type' => 'list']);
            $blog->contentItems()->where('slot_id', $listSlot->id)->delete();
            
            foreach ($dto->benefits as $item) {
                if(empty(trim($item))) continue;
                $blog->contentItems()->create(['slot_id' => $listSlot->id, 'text' => $item, 'position' => 0]);
            }
        }

        // Bloques
        if (!empty($dto->content_blocks)) {
            $blockSlot = BlogContentSlot::firstOrCreate(['name' => 'Bloques', 'data_type' => 'block']);
            $blog->contentBlocks()->where('slot_id', $blockSlot->id)->delete();
            
            foreach ($dto->content_blocks as $block) {
                $blog->contentBlocks()->create([
                    'slot_id' => $blockSlot->id,
                    'title' => $block['title'] ?? '',
                    'content' => $block['content'] ?? '',
                ]);
            }
        }
    }
}