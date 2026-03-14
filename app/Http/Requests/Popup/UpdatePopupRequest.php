<?php

namespace App\Http\Requests\Popup;

use Illuminate\Foundation\Http\FormRequest;

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
      'title' => 'sometimes|string|max:255',
      'button_text' => 'sometimes|string|max:255',
      'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
      'image_alt' => 'sometimes|string|max:255',
      'image_title' => 'nullable|string|max:255',
      'page_target' => 'sometimes|string',
      'delay_seconds' => 'sometimes|integer|min:0|max:60',
      'priority' => 'sometimes|integer|min:1|max:10',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date',
      'active' => 'sometimes|boolean',
    ];
  }
}
