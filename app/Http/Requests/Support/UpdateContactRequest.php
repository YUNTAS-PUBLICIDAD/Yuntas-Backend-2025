<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone'    => 'required|string|min:9|max:11',
            'district' => 'required|string|max:50',
            'message'  => 'required|string|max:150',
        ];
    }
}
