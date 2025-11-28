<?php

namespace App\Application\DTOs\Auth;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public string $deviceName
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            email: $request->validated('email'),
            password: $request->validated('password'),
            deviceName: $request->header('User-Agent', 'Unknown Device')
        );
    }
}