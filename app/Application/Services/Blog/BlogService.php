<?php

namespace App\Application\Services\Blog;

use App\Application\DTOs\Blog\BlogDTO;
use App\Domain\Repositories\Blog\BlogRepositoryInterface;
use App\Models\ImageSlot;
use App\Models\BlogContentSlot;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            // 1. Crear Blog
            $blog = $this->repository->save([
                'title' => $dto->title,
                'slug' => $dto->slug,
                'cover_subtitle' => $dto->cover_subtitle,
                'content' => $dto->content,
                'status' => $dto->status,
                'video_url' => $dto->video_url,
                'meta_title' => $dto->meta_title,
                'meta_description' => $dto->meta_description,
            ]);

            // 2. Imagen Principal
            if ($dto->main_image) {
            // Borrar anterior
            $oldMain = $blog->images()->whereHas('slot', fn($q) => $q->where('name', 'Main'))->first();
            if ($oldMain) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldMain->url));
                $oldMain->delete();
            }
            
            // Subir nueva
            $path = $dto->main_image->store('blogs/' . $blog->id, 'public');
            $mainSlot = ImageSlot::firstOrCreate(['name' => 'Main', 'module' => 'blogs']);
            
            $blog->images()->create([
                'slot_id' => $mainSlot->id, 
                'url' => '/storage/' . $path,
                'title' => $blog->title,
                'alt_text' => 'Portada actualizada'
            ]);
             }

            // 3. Galería
           if (!empty($dto->gallery_images)) {
            $gallerySlot = ImageSlot::firstOrCreate(['name' => 'Gallery', 'module' => 'blogs']);
            foreach ($dto->gallery_images as $img) {
                if (!$img instanceof \Illuminate\Http\UploadedFile) continue;
                $path = $img->store('blogs/' . $blog->id . '/gallery', 'public');
                $blog->images()->create(['slot_id' => $gallerySlot->id, 'url' => '/storage/' . $path]);
            }
        }

            // 4. Párrafos (Texto simple)
           if (!empty($dto->paragraphs)) {
            $textSlot = BlogContentSlot::firstOrCreate(['name' => 'Parrafos', 'data_type' => 'text']);
            $blog->contentTexts()->where('slot_id', $textSlot->id)->delete(); // Limpiar viejos
            foreach ($dto->paragraphs as $text) {
                $blog->contentTexts()->create(['slot_id' => $textSlot->id, 'content' => $text]);
            }
        }

            // 5. Beneficios (Listas)
            if (!empty($dto->benefits)) {
            $listSlot = BlogContentSlot::firstOrCreate(['name' => 'Beneficios', 'data_type' => 'list']);
            $blog->contentItems()->where('slot_id', $listSlot->id)->delete(); // Limpiar viejos
            foreach ($dto->benefits as $item) {
                $blog->contentItems()->create(['slot_id' => $listSlot->id, 'text' => $item, 'position' => 0]);
            }
        }
            // Bloques
          if (!empty($dto->content_blocks)) {
            $blockSlot = BlogContentSlot::firstOrCreate(['name' => 'Bloques', 'data_type' => 'block']);
            $blog->contentBlocks()->where('slot_id', $blockSlot->id)->delete(); // Limpiar viejos
            foreach ($dto->content_blocks as $block) {
                $blog->contentBlocks()->create([
                    'slot_id' => $blockSlot->id,
                    'title' => $block['title'] ?? '',
                    'content' => $block['content'] ?? '',
                ]);
            }
        }

        return $blog->refresh();

            });
    }

    public function delete(int $id): void
    {
        $blog = $this->repository->findById($id);
        if (!$blog) throw new ModelNotFoundException("Blog no encontrado");
        
        // Borrar imágenes del storage
        foreach ($blog->images as $img) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $img->url));
        }
        
        $this->repository->delete($id);
    }
}