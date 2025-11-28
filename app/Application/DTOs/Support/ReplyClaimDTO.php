<?php

namespace App\Application\DTOs\Support;

readonly class ReplyClaimDTO
{
    public function __construct(
        public int $admin_id,
        public string $message,
        public bool $send_email
    ) {}
}