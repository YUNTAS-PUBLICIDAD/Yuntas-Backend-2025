<?php

namespace App\Application\DTOs\Blog;

use Illuminate\Http\Request;

class BlogDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $hero_title,
        public string $cover_subtitle,       
        public string $status,
        public string $video_url,

        //  Producto
        public ?int $product_id,
        
        // SEO
        public string $meta_title,
        public string $meta_description, 
        
        // Relaciones y Archivos
        public $main_image,
        public ?string $main_image_title,
        public ?string $main_image_alt, 

        public ?array $gallery, // [['slot' => 'Hero', 'image' => File, 'alt' => '...'], ...]  
        public ?array $gallery_title, // esto solo es cuando se actualiza title de imagenes existentes 
        public ?array $gallery_alt, // esto solo es cuando se actualiza alt de imagenes existentes
        
        // Contenido Dinámico
        public string $description,  
        public array $benefits,   
        public string $testimonial,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->validated('name') ?? $request->input('name'), 
            slug: $request->validated('slug') ?? $request->input('slug'),
            hero_title: $request->validated('hero_title') ?? $request->input('hero_title'),
            cover_subtitle: $request->validated('cover_subtitle') ?? $request->input('cover_subtitle'),
            status: $request->input('status', 'published'),
            video_url: $request->validated('video_url') ?? $request->input('video_url'),

            product_id: $request->validated('product_id') ?? null,

            meta_title: $request->validated('meta_title') ?? $request->input('meta_title'),
            meta_description: $request->validated('meta_description') ?? $request->input('meta_description'),

            main_image: $request->file('main_image'),
            main_image_title: $request->input('main_image_title'),
            main_image_alt: $request->input('main_image_alt'), 

            gallery: self::processGallery($request),
            gallery_title: $request->input('gallery_title', []),
            gallery_alt: $request->input('gallery_alt', []),
            
            description: $request->validated('description') ?? $request->input('description'),
            benefits: $request->input('benefits', []),
            testimonial: $request->validated('testimonial') ?? $request->input('testimonial'),
        );
    }

    private static function processGallery(Request $request): array
    {
        $gallery = [];
        $galleryData = $request->input('gallery', []);

        foreach ($galleryData as $index => $item) {
            $imageFile = $request->file("gallery.{$index}.image");
            
            if ($imageFile) {
                $gallery[] = [
                    'slot' => $item['slot'] ?? 'Gallery',
                    'image' => $imageFile,
                    'title' => $item['title'] ?? null,
                    'alt' => $item['alt'] ?? null
                ];
            }
        }

        return $gallery;
    }
}