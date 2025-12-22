<?php

namespace App\Application\DTOs\Email;

class SendProductsEmailDTO
{
    public string $to;
    public string $subject;
    public array $productIds;
    public string $templateId;

    public function __construct(string $to, string $subject, array $productIds, string $templateId)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->productIds = $productIds;
        $this->templateId = $templateId;
    }
}
