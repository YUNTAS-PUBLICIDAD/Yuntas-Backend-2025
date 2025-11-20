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

        public ?string $meta_title,
        public ?string $meta_description,
        public array $keywords,

        public ?UploadedFile $main_image,
        public array $gallery_images = [],
        public array $specifications = [],
        public array $benefits = [],

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
            
            meta_title: $request->input('etiqueta.meta_titulo'),
            meta_description: $request->input('etiqueta.meta_descripcion'),
            keywords: $request->input('etiqueta.keywords', []),

            main_image: $request->file('imagen_principal'),
            gallery_images: $request->file('imagenes', []),
            specifications: $request->input('especificaciones', []),
            benefits: $request->input('beneficios', [])
        );
    }
}