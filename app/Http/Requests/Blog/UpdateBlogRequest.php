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
            'title' => 'required|string|max:150',
            'subtitulo' => 'nullable|string|max:255',
            'video_url' => 'nullable|url',

            'etiqueta' => 'nullable',

            'imagen_principal' => 'image|mimes:webp|max:5120',
            'imagen_principal_alt' => 'nullable|string|max:80',

            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|mimes:webp|max:5120',

            'imagenes_alts' => 'nullable|array',
            'imagenes_alts.*' => 'nullable|string|max:80',

            'product_id' => 'nullable|integer|exists:products,id',

            'descripciones' => 'nullable|array',
            'beneficios' => 'nullable|array',
            'testimonios' => 'nullable|array',
        ];
    }
}
