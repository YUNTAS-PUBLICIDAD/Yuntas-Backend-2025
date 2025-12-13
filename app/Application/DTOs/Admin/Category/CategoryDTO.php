<?php

namespace App\Application\DTOs\Admin\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description 
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->validated('name'),
            slug: $request->validated('slug') ?? Str::slug($request->validated('name')),
            description: $request->validated('description') 
        );
    }
}