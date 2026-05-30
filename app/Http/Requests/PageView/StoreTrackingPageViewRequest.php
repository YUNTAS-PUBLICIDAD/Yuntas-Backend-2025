<?php
 
namespace App\Http\Requests\PageView;
 
use Illuminate\Foundation\Http\FormRequest;
 
class StoreTrackingPageViewRequest extends FormRequest
{
    /**
     * Autorizar la petición.
     */
    public function authorize(): bool
    {
        return true;
    }
 
    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'route' => 'required|string|max:255',
            'session_id' => 'nullable|uuid',
        ];
    }
}
