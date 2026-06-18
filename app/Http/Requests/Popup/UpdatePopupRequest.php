<?php

namespace App\Http\Requests\Popup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePopupRequest extends FormRequest
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
      'lead_source_id' => 'sometimes|integer|exists:lead_sources,id',
      'title' => 'sometimes|string|max:255',
      'button_text' => 'sometimes|string|max:255',
      'button_color' => ['sometimes', 'nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
      'button_text_color' => ['sometimes', 'nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
      // 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
      // 'image_alt' => 'sometimes|string|max:255',
      // 'image_title' => 'nullable|string|max:255',
      // 'product_id' => 'sometimes|nullable|exists:products,id',
      // 'product_id' => [
      //   'sometimes',
      //   'nullable',
      //   'exists:products,id',
      //   Rule::requiredIf(fn () => $this->page_target === 'product-detail')
      // ],
      'product_id' => 'nullable|exists:products,id',
      'page_target' => 'sometimes|string',
      'delay_seconds' => 'sometimes|integer|min:0|max:60',
      'priority' => 'sometimes|integer|min:1|max:10',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date',
      'active' => 'sometimes|boolean',

        'images' => ['sometimes', 'array'],
        'images.*' => ['required', 'array'],
        'images.*.file' => 'required_with:images|image|mimes:jpg,jpeg,png,webp|max:2048',
        'images.*.device' => ['required_with:images', Rule::in(['desktop', 'mobile'])],
        'images.*.slot' => ['required_with:images', Rule::in(['left', 'right', 'center'])],
        'images.*.alt' => 'nullable|string|max:255',
        'images.*.title' => 'nullable|string|max:255',
    ];
  }
}
