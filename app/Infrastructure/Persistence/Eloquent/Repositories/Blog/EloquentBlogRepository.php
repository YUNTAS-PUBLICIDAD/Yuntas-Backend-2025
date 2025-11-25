<?php
namespace App\Infrastructure\Persistence\Eloquent\Repositories\Blog;

use App\Domain\Repositories\Blog\BlogRepositoryInterface;
use App\Models\Blog;

class EloquentBlogRepository implements BlogRepositoryInterface {
    public function paginate(int $perPage = 10) {
        // Cargamos relaciones clave
        return Blog::with(['images.slot', 'contentTexts', 'contentItems'])->latest()->paginate($perPage);
    }
    public function findBySlug(string $slug): ?Blog {
        return Blog::with(['images', 'contentTexts'])->where('slug', $slug)->first();
    }
    public function findById(int $id): ?Blog {
        return Blog::find($id);
    }
    public function save(array $data): Blog {
        return Blog::create($data);
    }
    public function delete(int $id): bool {
        return Blog::destroy($id) > 0;
    }
}