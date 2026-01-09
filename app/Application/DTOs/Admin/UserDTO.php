<?php

namespace App\Application\DTOs\Admin;

use Illuminate\Http\Request;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password, 
        public ?int $role_id      
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            role_id: $request->validated('role_id') 
        );
    }
}