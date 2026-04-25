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
            'contents' => 'sometimes|array',
            'contents.*.id' => 'sometimes|integer|exists:template_contents,id',
            'contents.*.channel' => 'sometimes|string',
            'contents.*.subject' => 'sometimes|string|nullable',
            'contents.*.content' => 'sometimes|string',
            'contents.*.variables' => 'sometimes|array',
            'contents.*.image' => 'sometimes|file|image',
            'contents.*.active' => 'sometimes|boolean',

            // Template buttons
            'contents.*.buttons' => 'sometimes|array',
            'contents.*.buttons.*.id' => 'sometimes|integer|exists:template_buttons,id',
            'contents.*.buttons.*.text' => 'sometimes|string|max:255',
            'contents.*.buttons.*.type' => 'sometimes|string',
            'contents.*.buttons.*.payload' => 'sometimes|array',
            'contents.*.buttons.*.order' => 'sometimes|integer',
            'contents.*.buttons.*.active' => 'sometimes|boolean',
        ];
    }
}
