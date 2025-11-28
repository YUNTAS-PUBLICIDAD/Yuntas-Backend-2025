<?php

namespace App\Application\DTOs\Support;

use Illuminate\Http\Request;

readonly class CreateContactDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public ?string $phone,
        public ?string $district,
        public ?string $request_detail,
        public ?string $message
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            first_name: $request->validated('first_name'),
            last_name: $request->validated('last_name', ''), 
            phone: $request->validated('phone'),
            district: $request->validated('district'),
            request_detail: $request->validated('request_detail'),
            message: $request->validated('message')
        );
    }
}