<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $getMainImageObj = fn($slotName) => $this->images->first(fn($img) => $img->slot?->name === $slotName);        
        
        $getGallery = fn($slotName) => $this->images->filter(fn($img) => $img->slot?->name === $slotName)->map(fn($img) => [
            'id' => $img->id,
            'url' => $img->url
        ])->values();

        $getContent = fn($slotName) => $this->contentItems->filter(fn($item) => $item->slot?->name === $slotName)->map(fn($item) => $item->text)->values();

        return [
            'id' => $this->id,
            'nombre' => $this->name, 
            'slug' => $this->slug,
            'titulo_corto' => $this->short_description,
            'descripcion' => $this->description,
            'precio' => $this->price,
            'estado' => $this->status,
            
          'imagen_principal' => [
                'url' => $getMainImageObj('Main')?->url,
                'alt' => $getMainImageObj('Main')?->alt_text,
                'title' => $getMainImageObj('Main')?->title,
            ],
            'galeria' => $this->images->filter(fn($img) => $img->slot?->name === 'Gallery')->map(fn($img) => [
                'id' => $img->id,
                'url' => $img->url,
                'title' => $img->title,
                'alt' => $img->alt_text,
            ])->values(),
            
            'especificaciones' => $getContent('Especificaciones'),
            'beneficios' => $getContent('Beneficios'),
            'seo' => [
            'meta_titulo' => $this->meta_title,
            'meta_descripcion' => $this->meta_description,
            'keywords' => $this->keywords ?? [],
        ],
            
            'creado_en' => $this->created_at->toIso8601String(),
        ];
    }
}