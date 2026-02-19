<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Blog;

use App\Domain\Repositories\Blog\BlogRepositoryInterface;
use App\Models\Blog;

class EloquentBlogRepository implements BlogRepositoryInterface
{
    public function paginate(int $perPage = 10)
    {
        return Blog::with(['images.slot'])
            ->latest()
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Blog
    {
        return Blog::with([
            'images.slot',        
            'contentTexts.slot',  
            'contentItems.slot',
        ])->where('slug', $slug)->first();
    }

    public function findById(int $id): ?Blog
    {
        return Blog::with(['images.slot'])->find($id);
    }

    public function save(array $data): Blog
    {
        return Blog::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $blog = Blog::find($id);
        return $blog ? $blog->update($data) : false;
    }

    public function delete(int $id): bool
    {
        return Blog::destroy($id) > 0;
    }
}