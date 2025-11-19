<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $getMainImage = fn($slotName) => $this->images->first(fn($img) => $img->slot?->name === $slotName)?->url;
        
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
            
            'imagen_principal' => $getMainImage('Main'),
            'galeria' => $getGallery('Gallery'),
            
            'especificaciones' => $getContent('Especificaciones'),
            'beneficios' => $getContent('Beneficios'),
            
            'creado_en' => $this->created_at->toIso8601String(),
        ];
    }
}