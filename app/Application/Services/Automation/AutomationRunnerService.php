<?php

namespace App\Application\Services\Automation;

use App\Application\Services\Channel\ChannelManagerService;
use App\Application\Services\Template\TemplateRenderService;
use App\Jobs\SendAutomationStepJob;
use App\Models\AutomationExecution;
use App\Models\AutomationLog;
use App\Models\TemplateStep;
use App\Services\Channel\EmailService;
use App\Services\Channel\WhatsAppService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomationRunnerService
{
    public function __construct(
        protected ChannelManagerService $channelManagerService,
        protected TemplateRenderService $templateRenderService
    ) {}

    // =====================================================
    // RUN ALL PENDING
    // =====================================================

    public function runPending(): void
    {
        $executions = AutomationExecution::query()
            ->where('status', 'running')
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', now())
            ->with([
                'lead',
                'template.steps.variants.assets',
                'template.steps.variants.productOverrides',
            ])
            ->get();

        foreach ($executions as $execution) {

            try {

                $this->processExecution($execution);

            } catch (Exception $e) {

                Log::error('AUTOMATION EXECUTION FAILED', [
                    'execution_id' => $execution->id,
                    'error' => $e->getMessage(),
                ]);

                $execution->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                ]);
            }
        }
    }

    // =====================================================
    // PROCESS EXECUTION
    // =====================================================

    protected function processExecution(
        AutomationExecution $execution
    ): void {

        $step = $execution->template
            ->steps
            ->where('step', $execution->current_step)
            ->first();

        // =========================================
        // FLOW FINISHED
        // =========================================

        if (!$step) {

            $execution->update([
                'status' => 'completed',
                'finished_at' => now(),
            ]);

            return;
        }

        DB::transaction(function () use (
            $execution,
            $step
        ) {

            $this->executeStep(
                $execution,
                $step
            );

            $this->scheduleNext(
                $execution,
                $step
            );
        });
    }

    // =====================================================
    // EXECUTE STEP
    // =====================================================

    protected function executeStep(
        AutomationExecution $execution,
        TemplateStep $step
    ): void {

        foreach ($step->variants as $variant) {

            if (!$variant->active) {
                continue;
            }

            SendAutomationStepJob::dispatch(
              $execution->id,
              $step->id,
              $variant->id
            );

            // try {

            //   // =====================================
            //             // RENDER PAYLOAD
            //             // =====================================

            //             $payload = $this->templateRenderService
            //                 ->render(
            //                     $execution->lead,
            //                     $variant
            //                 );

            //             // =====================================
            //             // SEND CHANNEL
            //             // =====================================

            //             $response = $this->channelManagerService
            //                 ->send(
            //                     $variant->channel,
            //                     $payload
            //                 );


            //     // =====================================
            //     // LOG SUCCESS
            //     // =====================================

            //     AutomationLog::create([

            //         'automation_execution_id'
            //             => $execution->id,

            //         'template_step_id'
            //             => $step->id,

            //         'template_variant_id'
            //             => $variant->id,

            //         'lead_id'
            //             => $execution->lead_id,

            //         'channel'
            //             => $variant->channel,

            //         'status'
            //             => 'success',

            //         'response'
            //             => $response,

            //         'sent_at'
            //             => now(),
            //     ]);

            // } catch (Exception $e) {

            //     Log::error('AUTOMATION STEP FAILED', [

            //         'execution_id'
            //             => $execution->id,

            //         'step_id'
            //             => $step->id,

            //         'variant_id'
            //             => $variant->id,

            //         'error'
            //             => $e->getMessage(),
            //     ]);

            //     // =====================================
            //     // LOG FAILED
            //     // =====================================

            //     AutomationLog::create([

            //         'automation_execution_id'
            //             => $execution->id,

            //         'template_step_id'
            //             => $step->id,

            //         'template_variant_id'
            //             => $variant->id,

            //         'lead_id'
            //             => $execution->lead_id,

            //         'channel'
            //             => $variant->channel,

            //         'status'
            //             => 'failed',

            //         'error'
            //             => $e->getMessage(),

            //         'sent_at'
            //             => now(),
            //     ]);
            // }
        }
    }

    // =====================================================
    // SCHEDULE NEXT
    // =====================================================

    protected function scheduleNext(
        AutomationExecution $execution,
        TemplateStep $currentStep
    ): void {

        $nextStep = $execution->template
            ->steps
            ->where('step', '>', $currentStep->step)
            ->sortBy('step')
            ->first();

        // =========================================
        // FINISH FLOW
        // =========================================

        if (!$nextStep) {

            $execution->update([
                'status' => 'completed',
                'finished_at' => now(),
            ]);

            return;
        }

        // =========================================
        // CALCULATE NEXT RUN
        // =========================================

        $nextRunAt = match ($nextStep->delay_unit) {

            'minutes'
                => now()->addMinutes(
                    $nextStep->delay_value
                ),

            'hours'
                => now()->addHours(
                    $nextStep->delay_value
                ),

            'days'
                => now()->addDays(
                    $nextStep->delay_value
                ),

            default
                => now(),
        };

        // =========================================
        // UPDATE EXECUTION
        // =========================================

        $execution->update([

            'current_step'
                => $nextStep->step,

            'next_run_at'
                => $nextRunAt,
        ]);
    }
}
