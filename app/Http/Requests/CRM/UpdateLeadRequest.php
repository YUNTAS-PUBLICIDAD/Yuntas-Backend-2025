<?php

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
            'product_id' => 'nullable|exists:products,id', 
            'source_id' => 'nullable|exists:lead_sources,id',
        ];
    }
}