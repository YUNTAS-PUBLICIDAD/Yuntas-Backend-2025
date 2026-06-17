<?php

namespace App\Http\Resources\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mainImage = $this->images->first(fn($img) => $img->slot->name === 'List' && $img->slot->module === 'blogs');
        $gallery = $this->images->filter(fn($img) => $img->slot->name !== 'List' && $img->slot->module === 'blogs')->values();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'hero_title' => $this->hero_title,
            'cover_subtitle' => $this->cover_subtitle,
            'video_url' => $this->video_url,
            'keywords' => $this->keywords,
            
            // SEO
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,

            // PRODUCTO ASOCIADO 
            'product' => $this->when(
                    $this->product, 
                    fn () => [
                        'id' => $this->product->id,
                        'name' => $this->product->name ?? $this->product->nombre,
                    ]
            ),
            
            // IMAGEN PRINCIPAL
            'main_image' => $mainImage ? [
                'url' => $mainImage->url, 
                'alt' => $mainImage->alt_text,
                'title' => $mainImage->title,
            ] : null,
            
            // GALERÍA
            'gallery' => $gallery->map(fn($img) => [
                'url' => $img->url,
                'alt' => $img->alt_text,
                'title' => $img->title,
                'slot' => $img->slot?->name 
            ]),
            
            // CONTENIDO DINÁMICO
            'description' => $this->contentTexts
                ->filter(fn($t) => $t->slot?->name === 'Descripciones')
                ->map(fn($t) => $t->content)
                ->values()->first(),

            'testimonial' => $this->contentTexts
                ->filter(fn($t) => $t->slot?->name === 'Testimonios')
                ->map(fn($t) => $t->content)
                ->values()->first(),

            'benefits' => $this->contentItems
                ->filter(fn($i) => $i->slot?->name === 'Beneficios')
                ->map(fn($i) => $i->text)
                ->values(),

            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}