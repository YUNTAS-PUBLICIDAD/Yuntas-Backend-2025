<?php

namespace App\Application\DTOs\Support;

use Illuminate\Http\Request;

readonly class CreateClaimDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public int $document_type_id,
        public string $document_number,
        public string $email,
        public ?string $phone,
        public int $claim_type_id, 
        public string $detail,
        public ?int $product_id,
        public ?string $purchase_date, 
        public ?float $claimed_amount
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            first_name: $request->validated('first_name'),
            last_name: $request->validated('last_name'),
            document_type_id: $request->validated('document_type_id'),
            document_number: $request->validated('document_number'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
            claim_type_id: $request->validated('claim_type_id'),
            detail: $request->validated('detail'),
            product_id: $request->validated('product_id'),
            purchase_date: $request->validated('purchase_date'),
            claimed_amount: $request->validated('claimed_amount')
        );
    }
}