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
            'titulo' => 'required|string|max:150',
            'subtitulo' => 'nullable|string|max:255',
            'contenido' => 'required|string',
            'url_video' => 'nullable|url',

            'etiqueta' => 'nullable',

            // 🔑 Imagen YA NO es required
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'imagen_principal_alt' => 'nullable|string|max:191',

            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',

            'imagenes_alts' => 'nullable|array',
            'imagenes_alts.*' => 'nullable|string|max:191',

            'categorias' => 'nullable|array',
            'categorias.*' => 'integer|exists:categories,id',

            'parrafos' => 'nullable|array',
            'beneficios' => 'nullable|array',
            'bloques' => 'nullable|array',
        ];
    }
}
