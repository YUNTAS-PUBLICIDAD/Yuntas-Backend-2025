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
            'titulo' => 'required|string|max:150',
            'subtitulo' => 'nullable|string|max:255',
            'url_video' => 'nullable|url',
            
            // SEO (Puede venir como JSON string o array, validamos keys si es array)
            'etiqueta' => 'nullable', 

            // Imagen Principal
            'imagen_principal' => 'required|image|mimes:webp|max:5120',
            'imagen_principal_alt' => 'nullable|string|max:80',

            // Galería
            'imagenes' => 'nullable|array', 
            'imagenes.*' => 'image|mimes:webp|max:5120',
            'imagenes_alts' => 'nullable|array', 
            'imagenes_alts.*' => 'nullable|string|max:80',

            // Contenido Dinámico
            'parrafos' => 'nullable|array',
            'parrafos.*' => 'string',
            
            'beneficios' => 'nullable|array',
            'beneficios.*' => 'string',

            // Producto asociado
            'product_id' => 'nullable|exists:products,id',
        ];
    }
}