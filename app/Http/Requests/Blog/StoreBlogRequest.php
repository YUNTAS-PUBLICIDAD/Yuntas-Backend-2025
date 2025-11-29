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
            'titulo' => 'required|string|max:150',
            'subtitulo' => 'nullable|string|max:255',
            'contenido' => 'nullable|string', 
            
          
            'imagen_principal' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'imagenes' => 'nullable|array', 
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',

            'parrafos' => 'nullable|array',
            'parrafos.*' => 'string',
            'beneficios' => 'nullable|array',
            'beneficios.*' => 'string',
           
            
            'etiqueta' => 'nullable', 
        ];
    }
}