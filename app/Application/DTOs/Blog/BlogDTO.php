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
        
        // Relaciones
        public array $categories,      

        // Archivos
        public ?UploadedFile $main_image,
        public ?string $main_image_alt, 
        public array $gallery_images = [],
        public array $gallery_alts = [],    
        
        // Contenido Dinámico
        public array $paragraphs = [],  
        public array $benefits = [],     
        public array $content_blocks = []
    ) {}

    public static function fromRequest($request): self
{
    $seo = is_string($request->input('etiqueta')) 
        ? json_decode($request->input('etiqueta'), true) 
        : $request->input('etiqueta', []);

    $title = $request->validated('titulo');

    return new self(
        title: $title,
        slug: Str::slug($title),
        cover_subtitle: $request->validated('subtitulo'),
        content: $request->validated('contenido'),
        status: 'published',
        video_url: $request->validated('url_video'),

        meta_title: $seo['meta_titulo'] ?? null,
        meta_description: $seo['meta_descripcion'] ?? null,

        categories: $request->validated('categorias') ?? [],

        main_image: $request->file('imagen_principal'),
        main_image_alt: $request->input('imagen_principal_alt'),

        gallery_images: $request->file('imagenes', []),
        gallery_alts: $request->input('imagenes_alts', []),

        paragraphs: $request->input('parrafos', []),
        benefits: $request->input('beneficios', []),
        content_blocks: $request->input('bloques', [])
    );
}
}