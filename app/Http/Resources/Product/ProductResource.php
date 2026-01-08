<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        
        $mainImage = $this->images->first(fn($img) => $img->slot?->name === 'List' || $img->slot?->name === 'Main');
        $gallery = $this->images->filter(fn($img) => $img->slot?->name !== 'List' && $img->slot?->name !== 'Main')->values();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'price' => $this->price,
            'hero_title' => $this->hero_title,
            'description' => $this->description,
            'status' => $this->status,
            
            // SEO
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'keywords' => $this->keywords,

            'images' => $this->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'url' => $img->url,
                    'alt_text' => $img->alt_text, 
                    'slot_name' => $img->slot?->name ?? 'Gallery', 
                ];
            }),
            
            'main_image' => $mainImage ? [
                'url' => $mainImage->url, 
                'alt' => $mainImage->alt_text,
                'title' => $mainImage->title,
            ] : null,
            
            'gallery' => $gallery->map(fn($img) => [
                'url' => $img->url,
                'alt' => $img->alt_text,
                'slot' => $img->slot?->name 
            ]),

            //  CATEGORÍAS
            'categories' => $this->categories->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug
            ]),

            //  CONTENIDO (Specs, Benefits)
            'specifications' => $this->contentItems
                ->filter(fn($i) => $i->slot?->name === 'Especificaciones')
                ->map(fn($i) => $i->text)
                ->values(),

            'benefits' => $this->contentItems
                ->filter(fn($i) => $i->slot?->name === 'Beneficios')
                ->map(fn($i) => $i->text)
                ->values(),

            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}