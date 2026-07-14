<?php
namespace App\Application\Services\Automation;

use App\Models\AutomationExecution;
use App\Models\Lead;
use App\Models\Template;

use Illuminate\Support\Facades\Log;

// Solo inicia flujos
class AutomationExecutionService
{
    public function start(
        Lead $lead,
        string $context
    ): ?AutomationExecution {

        $template = Template::query()
            ->where('context', $context)
            ->where('active', true)
            ->first();

        if (!$template) {
            Log::warning("No se pudo iniciar la automatización para el lead ID {$lead->id} porque no hay una plantilla activa para el contexto '{$context}'.");
            return null;
        }

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
