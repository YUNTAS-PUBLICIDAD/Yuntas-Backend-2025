<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambia según tu lógica de permisos
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:categories,name',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/u', 
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe una categoría con ese nombre.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'description.max' => 'La descripción no puede superar los 255 caracteres.',
        ];
    }
}
