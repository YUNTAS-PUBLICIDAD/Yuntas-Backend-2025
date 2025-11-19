<?php

namespace App\Application\DTOs\Product; 
use Illuminate\Http\UploadedFile;

readonly class ProductDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $short_description,
        public ?string $description,
        public float $price,
        public string $status,
        public ?UploadedFile $main_image,
        public array $gallery_images = [],
        public array $specifications = [],
        public array $benefits = [],
        public array $seo = []
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            name: $request->validated('nombre'),
            slug: $request->validated('link') ?? \Str::slug($request->validated('nombre')),
            short_description: $request->validated('titulo'),
            description: $request->validated('descripcion'),
            price: (float) $request->validated('precio', 0),
            status: 'active',
            main_image: $request->file('imagen_principal'),
            gallery_images: $request->file('imagenes', []),
            specifications: $request->input('especificaciones', []),
            benefits: $request->input('beneficios', []),
            seo: $request->input('etiqueta', [])
        );
    }
}