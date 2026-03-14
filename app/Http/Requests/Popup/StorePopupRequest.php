<?php

namespace App\Http\Requests\Popup;

use Illuminate\Foundation\Http\FormRequest;

class StorePopupRequest extends FormRequest
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
      'title' => 'required|string|max:255',
      'button_text' => 'required|string|max:255',
      'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
      'image_alt' => 'required|string|max:255',
      'image_title' => 'nullable|string|max:255',
      'page_target' => 'required|string',
      'delay_seconds' => 'required|integer|min:0|max:60',
      'priority' => 'required|integer|min:1|max:10',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date',
      'active' => 'sometimes|boolean'
    ];
  }
}
