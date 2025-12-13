<?php

namespace App\Application\DTOs\Product;

use Illuminate\Http\Request;

class ProductDTO
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
        public ?array $keywords,
        
        // Relaciones y Archivos
        public ?array $categories,        
        public $main_image,
        public ?string $main_image_alt,   
        public ?array $gallery_images,
        public ?array $gallery_alts,      
        
        // Contenido
        public ?array $specifications,
        public ?array $benefits
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->validated('name') ?? $request->input('name'),
            slug: $request->validated('slug') ?? $request->input('slug'),
            short_description: $request->input('short_description'),
            description: $request->input('description'),
            price: (float) $request->input('price'),
            status: $request->input('status', 'active'),
            meta_title: $request->input('meta_title'),
            meta_description: $request->input('meta_description'),
            keywords: is_string($request->input('keywords')) 
                ? explode(',', $request->input('keywords')) 
                : $request->input('keywords', []),

            categories: $request->input('categories', []), 
            
            main_image: $request->file('main_image'),
            main_image_alt: $request->input('main_image_alt'), 

            gallery_images: $request->file('gallery_images', []),
            gallery_alts: $request->input('gallery_alts', []), 

            specifications: $request->input('specifications', []),
            benefits: $request->input('benefits', [])
        );
    }
}