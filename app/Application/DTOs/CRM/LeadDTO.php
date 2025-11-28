<?php

namespace App\Application\DTOs\CRM;

use Illuminate\Http\Request;

readonly class LeadDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $message,
        public ?int $product_id, 
        public ?int $source_id   
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
            message: $request->validated('message'),
            product_id: $request->validated('product_id'),
            source_id: $request->validated('source_id')
        );
    }
}