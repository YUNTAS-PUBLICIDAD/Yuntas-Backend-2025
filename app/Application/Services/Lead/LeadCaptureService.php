<?php
namespace App\Application\Services\Lead;

use App\Application\Services\Automation\AutomationExecutionService;
use App\Models\Lead;

// Solo captura leads
class LeadCaptureService
{
    public function __construct(
        protected AutomationExecutionService $automationExecutionService
    ) {}

    public function capture(array $data): Lead
    {
        $lead = Lead::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'message' => $data['message'] ?? null,
                'product_id' => $data['product_id'] ?? null,
                'source_id' => $data['source_id'],
            ]
        );

        $context =
            $lead->product_id
                ? 'PRODUCTO'
                : 'INICIO';

        $this->automationExecutionService
            ->start(
                $lead,
                $context
            );

        return $lead;
    }
}
