<?php

namespace App\Http\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateRequest extends FormRequest
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
        'lead_source_id' => 'sometimes|integer',
               'name' => 'sometimes|string|max:255',
               'active' => 'sometimes|boolean',

               'contents' => 'required|array|min:1',

               'contents.*.id' => 'nullable|integer|exists:template_contents,id',

               'contents.*.channel' => 'required_with:contents.*.content|in:whatsapp,email',
               'contents.*.subject' => 'nullable|string',
               'contents.*.content' => 'required|string',

               'contents.*.variables' => 'array',
               'contents.*.variables.*' => 'string',

               'contents.*.image' => 'nullable|file|image|mimes:webp|max:1024',
               'contents.*.active' => 'required|boolean',

               // Buttons
               'contents.*.buttons' => 'array',
               'contents.*.buttons.*.id' => 'nullable|integer|exists:template_buttons,id',
               'contents.*.buttons.*.text' => 'required|string|max:255',
               'contents.*.buttons.*.type' => 'required|in:url',
               'contents.*.buttons.*.payload' => 'required|array',
               'contents.*.buttons.*.payload.url' => 'required|url',
               'contents.*.buttons.*.order' => 'nullable|integer',
               'contents.*.buttons.*.active' => 'required|boolean',
        ];
    }
}
