<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Datos Básicos
            'title' => 'required|string|max:150',
            'slug' => 'required|string|max:150|unique:blogs,slug,',
            'hero_title' => 'required|string|max:150',
            'cover_subtitle' => 'required|string|max:255',
            'video_url' => 'nullable|url',

            // SEO 
            'meta_title' => 'required|string|max:70', 
            'meta_description' => 'required|string|max:160',

            // Imagen Principal
            'main_image' => 'nullable|image|mimes:webp|max:5120',
            'main_image_title' => 'nullable|string|max:50',
            'main_image_alt' => 'nullable|string|max:80',

            // Galería
            'gallery' => 'nullable|array',
            'gallery.*.slot' => 'nullable|string|in:Hero,Description,Benefits,Testimonial',
            'gallery.*.image' => 'nullable|image|mimes:webp|max:5120',
            'gallery.*.title' => 'nullable|string|max:50',
            'gallery.*.alt' => 'nullable|string|max:80',
            'gallery_alt.*' => 'nullable|string|max:255',

            // Contenido Dinámico
            'description' => 'required|string',
            
            'benefits' => 'required|array',
            'benefits.*' => 'string|max:150',

            'testimonial' => 'nullable|string|max:255',

            // Producto asociado
            'product_id' => 'required|integer|exists:products,id', // titulo (nombre) en card
        ];
    }
}
