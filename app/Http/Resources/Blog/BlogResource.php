<?php

namespace App\Http\Resources\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 1. Separar Imagen Principal de la Galería
        $mainImage = $this->images->first(fn($img) => $img->slot?->name === 'Main');
        $gallery = $this->images->filter(fn($img) => $img->slot?->name === 'Gallery')->values();

        // 2. Filtrar Contenido Dinámico por Slot
        $paragraphs = $this->contentTexts
            ->filter(fn($t) => $t->slot?->name === 'Parrafos')
            ->map(fn($t) => $t->content)
            ->values();

        $benefits = $this->contentItems
            ->filter(fn($i) => $i->slot?->name === 'Beneficios')
            ->map(fn($i) => $i->text)
            ->values();

        $blocks = $this->contentBlocks
            ->filter(fn($b) => $b->slot?->name === 'Bloques')
            ->map(fn($b) => [
                'title' => $b->title,
                'content' => $b->content
            ])
            ->values();

        // 3. JSON
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'cover_subtitle' => $this->cover_subtitle,
            'content' => $this->content, 
            'video_url' => $this->video_url,
            'status' => $this->status,
            'created_at' => $this->created_at->toIso8601String(),
            
            // SEO
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,

            // CATEGORÍAS 
            'categories' => $this->categories->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug
            ]),

            // PRODUCTO ASOCIADO 
            'product' => $this->when(
            $this->product,
            fn () => [
            'id' => $this->product->id,
            'name' => $this->product->name ?? $this->product->nombre,
            'slug' => $this->product->slug ?? null,
    ]
),
            
            // IMAGEN PRINCIPAL (Objeto completo con ALT)
            'main_image' => $mainImage ? [
                'url' => $mainImage->url,
                'alt' => $mainImage->alt_text,
            ] : null,
            
            // GALERÍA
            'gallery' => $gallery->map(fn($img) => [
                'url' => $img->url,
                'alt' => $img->alt_text
            ]),
            
            // CONTENIDO DINÁMICO
            'paragraphs' => $paragraphs,
            'benefits' => $benefits,
            'content_blocks' => $blocks,
        ];
    }
}