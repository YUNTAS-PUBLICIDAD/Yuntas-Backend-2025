<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'first_name'       => 'required|string|max:150',
            'last_name'        => 'required|string|max:150',
            'document_type_id' => 'required|integer|exists:document_types,id',
            'document_number'  => 'required|string|max:20',
            'email'            => 'required|email|max:191',
            'phone'            => 'nullable|string|max:20',
            
            'claim_type_id'    => 'required|integer|exists:claim_types,id',
            
            'product_id'       => 'nullable|integer|exists:products,id',
            
            'purchase_date'    => 'nullable|date',
            'claimed_amount'   => 'nullable|numeric|min:0',
            'detail'           => 'required|string',
        ];
    }
}