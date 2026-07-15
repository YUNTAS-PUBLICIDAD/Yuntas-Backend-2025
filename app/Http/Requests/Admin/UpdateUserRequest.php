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
            'role_id' => [
                'nullable',
                'integer',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    $targetRole = \App\Models\Role::find($value);
                    if ($targetRole && $targetRole->name === 'admin') {
                        $currentUser = $this->user();
                        if (!$currentUser || !$currentUser->role || $currentUser->role->name !== 'admin') {
                            $fail('No tienes permisos para asignar el rol de Administrador.');
                        }
                    }
                }
            ],
        ];
    }
}