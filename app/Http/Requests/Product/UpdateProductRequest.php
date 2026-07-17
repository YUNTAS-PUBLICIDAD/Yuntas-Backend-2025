<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('id');

        return [
            // Datos Basicos
            'name' => 'required|string|max:150',
            'slug' => [
                'required',
                'string',
                'max:160',
                Rule::unique('products', 'slug')->ignore($productId)
            ],
            'price' => 'required|numeric|min:0',
            'hero_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'video_url' => 'nullable|url|required_with:video_description,video_subtitle',
            'video_description' => 'nullable|string',
            'video_subtitle' => 'nullable|string|max:255',

            // SEO
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'keywords' => 'nullable',

            // Imagen Principal
            'main_image' => 'nullable|image|mimes:webp|max:5120',
            'main_image_title' => 'nullable|string|max:50',
            'main_image_alt' => 'nullable|string|max:80',

            // Galería
            'gallery' => 'nullable|array',
            // 'gallery.*.slot' => 'nullable|string|in:Hero,Specs,Benefits,Popups,Gallery',
            'gallery.*.slot' => [
                'nullable',
                'string',
                'in:Hero,Specs,Benefits,PopupLeft,PopupRight,PopupMobile'
            ],
            'gallery.*.image' => 'nullable|image|mimes:webp|max:5120',
            'gallery.*.title' => 'nullable|string|max:50',
            'gallery.*.alt' => 'nullable|string|max:80',
            'gallery_alt.*' => 'nullable|string|max:255',

            // Relaciones
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:150',

            // Contenido Dinámico
            'specifications' => 'nullable|array',
            'benefits' => 'nullable|array',
        ];
    }
}
