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

               'contents' => 'required|array|min:1',

               'contents.*.channel' => 'required|in:whatsapp,email',
               'contents.*.subject' => 'nullable|string',
               'contents.*.content' => 'required|string',
               'contents.*.variables' => 'array',
               'contents.*.variables.*' => 'string',
               'contents.*.image' => 'nullable|file|image|mimes:webp|max:1024',
               'contents.*.active' => 'required|boolean',

               // Buttons
               'contents.*.buttons' => 'array',
               'contents.*.buttons.*.text' => 'required|string|max:255',
               'contents.*.buttons.*.type' => 'required|in:url',
               'contents.*.buttons.*.payload' => 'required|array',
               'contents.*.buttons.*.payload.url' => 'required|url',
               'contents.*.buttons.*.order' => 'nullable|integer',
               'contents.*.buttons.*.active' => 'required|boolean',
        ];
    }
}
