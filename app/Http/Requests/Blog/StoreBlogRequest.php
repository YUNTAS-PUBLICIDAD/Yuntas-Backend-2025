<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Datos Básicos
            'name' => 'required|string|max:150', // titulo en card
            'slug' => 'nullable|string|max:150|unique:blogs,slug', // slug
            'hero_title' => 'required|string|max:150', // titulo hero en detalle
            'cover_subtitle' => 'nullable|string|max:255', // subtitulo en card y detalle
            'video_url' => 'nullable|url', // url de video opcional
            
            // SEO 
            'meta_title' => 'nullable|string|max:70', 
            'meta_description' => 'nullable|string|max:160',

            // Imagen Principal
            'main_image' => 'required|image|mimes:webp|max:5120', // imagen princial en card
            'main_image_title' => 'nullable|string|max:50',
            'main_image_alt' => 'nullable|string|max:80',

            // Galería
            'gallery' => 'nullable|array', // imagen para hero, descripciones, beneficios o testimonios
            'gallery.*.slot' => 'required|string|in:Hero,Description,Benefits,Testimonial',
            'gallery.*.image' => 'required|image|mimes:webp|max:5120',
            'gallery.*.title' => 'nullable|string|max:50',
            'gallery.*.alt' => 'nullable|string|max:80',

            // Contenido Dinámico
            'description' => 'nullable|string', // descripcion en detalle
            
            'benefits' => 'nullable|array', // beneficios en detalle
            'benefits.*' => 'string',

            'testimonial' => 'nullable|string|max:255', // testimonio en detalle

            // Producto asociado
            'product_id' => 'nullable|exists:products,id',
        ];
    }
}