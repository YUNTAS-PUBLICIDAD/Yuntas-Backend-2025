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
            'nombre' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            
            'imagen_principal' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            
           
            'etiqueta' => 'nullable|array',
            'especificaciones' => 'nullable|array',
            'beneficios' => 'nullable|array',
        ];
    }
}