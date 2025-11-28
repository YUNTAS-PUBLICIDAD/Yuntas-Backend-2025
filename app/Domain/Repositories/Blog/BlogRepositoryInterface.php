<?php
namespace App\Domain\Repositories\Blog;
use App\Models\Blog;

interface BlogRepositoryInterface {
    public function paginate(int $perPage = 10);
    public function findBySlug(string $slug): ?Blog;
    public function findById(int $id): ?Blog;
    public function save(array $data): Blog;
    public function delete(int $id): bool;
}