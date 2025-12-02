<?php

namespace App\Application\DTO;

class SendProductsEmailDTO
{
    public string $to;
    public string $subject;
    public array $productIds;

    public function __construct(string $to, string $subject, array $productIds)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->productIds = $productIds;
    }
}
