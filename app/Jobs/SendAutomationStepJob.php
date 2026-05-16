<?php

namespace App\Jobs;

use App\Application\Services\Channel\ChannelManagerService;
use App\Application\Services\Template\TemplateRenderService;
use App\Models\AutomationExecution;
use App\Models\AutomationLog;
use App\Models\TemplateVariant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Notifications\ChannelManager;

class SendAutomationStepJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
    public int $executionId,
    public int $stepId,
    public int $variantId
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
    TemplateRenderService $renderService,
    ChannelManagerService $channelManager
    ): void
    {

      $execution =
                AutomationExecution::with('lead')
                ->findOrFail($this->executionId);

            $variant =
                TemplateVariant::with([
                    'assets',
                    'productOverrides'
                ])->findOrFail($this->variantId);

            $payload = $renderService->render(
                $execution->lead,
                $variant
            );

            $response = $channelManager->send(
                $variant->channel,
                $payload
            );

            AutomationLog::create([
                'automation_execution_id'
                    => $execution->id,

                'template_step_id'
                    => $this->stepId,

                'template_variant_id'
                    => $variant->id,

                'lead_id'
                    => $execution->lead_id,

                'channel'
                    => $variant->channel,

                'status'
                    => 'success',

                'response'
                    => $response,

                'sent_at'
                    => now(),
            ]);

    }
}
