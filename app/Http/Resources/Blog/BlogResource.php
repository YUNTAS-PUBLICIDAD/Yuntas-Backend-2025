<?php

namespace App\Http\Resources\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Helpers locales para filtrar por slot
        $getMainImage = fn() => $this->images->first(fn($i) => $i->slot?->name === 'Main');
        $getTexts = fn($slot) => $this->contentTexts->filter(fn($t) => $t->slot?->name === $slot)->pluck('content');
        $getItems = fn($slot) => $this->contentItems->filter(fn($i) => $i->slot?->name === $slot)->pluck('text');

        return [
            'id' => $this->id,
            'titulo' => $this->title,
            'slug' => $this->slug,
            'subtitulo' => $this->cover_subtitle,
            'contenido_principal' => $this->content,
            'fecha' => $this->created_at->format('Y-m-d'),
            
            // Imagen Principal
            'imagen' => $getMainImage()?->url,
            'imagen_alt' => $getMainImage()?->alt_text,
            
            // Contenido Dinámico reconstruido
            'parrafos' => $getTexts('Parrafos'),
            'beneficios' => $getItems('Beneficios'),
            
            // SEO (Si agregaste las columnas)
            // 'meta_titulo' => $this->meta_title,
        ];
    }
}