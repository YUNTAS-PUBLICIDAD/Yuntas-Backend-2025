<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:150',
            'precio' => 'sometimes|numeric|min:0',
            
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', 
            
            'etiqueta' => 'nullable|array',
            'especificaciones' => 'nullable|array',
            'beneficios' => 'nullable|array',
        ];
    }
}