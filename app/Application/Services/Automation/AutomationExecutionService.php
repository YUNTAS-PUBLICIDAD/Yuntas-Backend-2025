<?php
namespace App\Application\Services\Automation;

use App\Models\AutomationExecution;
use App\Models\Lead;
use App\Models\Template;

// Solo inicia flujos
class AutomationExecutionService
{
    public function start(
        Lead $lead,
        string $context
    ): AutomationExecution {

        $template = Template::query()
            ->where('context', $context)
            ->where('active', true)
            ->firstOrFail();

        return AutomationExecution::create([

            'template_id' => $template->id,

            'lead_id' => $lead->id,

            'current_step' => 1,

            'status' => 'running',

            'started_at' => now(),

            'next_run_at' => now(),
        ]);
    }
}
