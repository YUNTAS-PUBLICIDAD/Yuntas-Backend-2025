<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'first_name'       => 'required|string|max:20',
            'last_name'        => 'nullable|string|max:20',
            'phone'            => 'required|string|min:9|max:11',
            'district'         => 'nullable|string|max:50',
            'request_detail'   => 'nullable|string|max:100',
            'message'            => 'required|string|max:150',
        ];
    }
}