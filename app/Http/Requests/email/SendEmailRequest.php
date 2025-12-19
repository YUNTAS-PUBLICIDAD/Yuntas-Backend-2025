<?php

namespace App\Http\Requests\Email;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',


            // ➕ NUEVO
            'productos' => 'required|array|min:1',
            'productos.*' => 'integer|exists:products,id',
        ];
    }
}
