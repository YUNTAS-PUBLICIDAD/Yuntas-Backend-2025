<?php

namespace App\Http\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;

class StoreTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'lead_source_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'active' => 'required|boolean',
            'contents' => 'array',
            'contents.*.channel' => 'required|string',
            'contents.*.subject' => 'nullable|string',
            'contents.*.content' => 'required|string',
            'contents.*.variables' => 'array',
            'contents.*.image' => 'nullable|file|image',
            'contents.*.active' => 'required|boolean',
        ];
    }
}
