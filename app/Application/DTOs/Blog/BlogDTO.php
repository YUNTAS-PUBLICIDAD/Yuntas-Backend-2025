<?php

namespace App\Application\DTOs\Blog;

use Illuminate\Http\UploadedFile;

readonly class BlogDTO
{
    public function __construct(
        public string $title,
        public string $slug,
        public ?string $cover_subtitle, 
        public string $content,        
        public string $status,
        public ?string $video_url,
        
        // SEO
        public ?string $meta_title,
        public ?string $meta_description,
        
        // Archivos
        public ?UploadedFile $main_image,
        public array $gallery_images = [],
        
       
        public array $paragraphs = [],  
        public array $benefits = [],     

        public array $content_blocks = []
    ) {}

    public static function fromRequest($request): self
    {
        $seo = is_string($request->input('etiqueta')) 
            ? json_decode($request->input('etiqueta'), true) 
            : $request->input('etiqueta', []);

        return new self(
            title: $request->validated('titulo'), 
            slug: \Str::slug($request->validated('titulo')),
            cover_subtitle: $request->validated('subtitulo'),
            content: $request->validated('contenido') ?? '', 
            status: 'published', 
            video_url: $request->validated('url_video'),
            
            meta_title: $seo['meta_titulo'] ?? null,
            meta_description: $seo['meta_descripcion'] ?? null,
            
            main_image: $request->file('imagen_principal'),
            gallery_images: $request->file('imagenes', []),
            
            paragraphs: $request->input('parrafos', []),
            benefits: $request->input('beneficios', []),
            content_blocks: $request->input('bloques', [])
        );
        
    }
}