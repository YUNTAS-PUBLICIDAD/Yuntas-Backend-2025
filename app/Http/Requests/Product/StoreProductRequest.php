<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Datos Básicos
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:160|unique:products,slug',
            'price' => 'required|numeric|min:0',
            'hero_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'video_url' => 'nullable|url',

            // SEO
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'keywords' => 'nullable',

            // Imagen Principal
            'main_image' => 'required|image|mimes:webp|max:5120',
            'main_image_title' => 'nullable|string|max:50',
            'main_image_alt' => 'nullable|string|max:80',

            // Galería
            'gallery' => 'nullable|array',
            // 'gallery.*.slot' => 'required|string|in:Hero,Specs,Benefits,Popups,Gallery',
            'gallery.*.slot' => [
                'required',
                'string',
                'in:Hero,Specs,Benefits,PopupLeft,PopupRight,PopupMobile'
            ],
            'gallery.*.image' => 'required|image|mimes:webp|max:5120',
            'gallery.*.title' => 'nullable|string|max:50',
            'gallery.*.alt' => 'nullable|string|max:80',

            // Relaciones
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:150',

            // Contenido Dinámico
            'specifications' => 'nullable|array',
            'benefits' => 'nullable|array',
        ];
    }
}
