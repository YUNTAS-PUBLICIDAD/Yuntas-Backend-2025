<?php

namespace App\Http\Requests\Popup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    $isProduct = $this->page_target === 'product-detail';
    return [
      'lead_source_id' => 'required|integer|exists:lead_sources,id',
      'title' => 'required|string|max:255',
      'button_text' => 'required|string|max:255',
      'button_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
      // 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
      // 'image_alt' => 'required|string|max:255',
      // 'image_title' => 'nullable|string|max:255',
      'page_target' => 'required|string',
      'delay_seconds' => 'required|integer|min:0|max:60',
      'priority' => 'required|integer|min:1|max:10',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date',
      'active' => 'sometimes|boolean',
      'product_id'=> 'nullable|exists:products,id',
      // 'product_id' => [
      //     'nullable',
      //     'exists:products,id',
      //     Rule::requiredIf(fn () => $this->page_target === 'product-detail')
      // ],

      // Imágenes
      // 'images' => ['required', 'array', 'size:3'],
      'images' => [
                 Rule::requiredIf(!$isProduct),
                 'array',
             ],
      // 'images' => [
      //     Rule::requiredIf(fn () => $this->page_target !== 'product-detail'),
      //     'array'
      // ],
      // 'images' => [
      //     Rule::requiredIf(fn () => $this->page_target !== 'product-detail'),
      //     'sometimes',
      //     'array'
      // ],

      'images.*' => ['required', 'array'],
      // 'images.*' => [
      //     Rule::requiredIf(fn () => $this->page_target !== 'product-detail'),
      //     'array'
      // ],
      'images.*.file' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
      // 'images.*.file' => [
      //     Rule::requiredIf(fn () => $this->page_target !== 'product-detail'),
      //     'image',
      //     'mimes:jpg,jpeg,png,webp',
      //     'max:2048'
      // ],
      // 'images.*.file' => [
      //     'sometimes',
      //     'image',
      //     'mimes:jpg,jpeg,png,webp',
      //     'max:2048'
      // ],

     'images.*.device' => ['required', Rule::in(['desktop', 'mobile'])],
     // 'images.*.device' => [
     //     'sometimes',
     //     Rule::in(['desktop', 'mobile'])
     // ],
     'images.*.slot' => ['required', Rule::in(['left', 'right', 'center'])],
     // 'images.*.slot' => [
     //     'sometimes',
     //     Rule::in(['left', 'right', 'center'])
     // ],
     'images.*.alt' => 'nullable|string|max:255',
     'images.*.title' => 'nullable|string|max:255',
    ];
  }
}
