<?php

namespace App\Application\DTOs\Support;

use Illuminate\Http\Request;

readonly class UpdateContactDTO
{
    public function __construct(
        public string $phone,
        public string $district,
        public string $message
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            phone: $request->validated('phone'),
            district: $request->validated('district'),
            message: $request->validated('message')
        );
    }
}
