<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id'); 

        return [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:6', 
            'role' => 'nullable|string|exists:roles,name',
        ];
    }
}