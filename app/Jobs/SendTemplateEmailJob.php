<?php

namespace App\Jobs;

use App\Application\Services\Template\TemplateService;
use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Log;

class SendTemplateEmailJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
      public int $leadId,
      public int $productId,
      // public int $sourceId,
      public int $step,
      public string $channel,
      public array $data
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(TemplateService $templateService): void
    {
        $lead = Lead::find($this->leadId);
        if (!$lead){
          Log::warning("LEAD NOT FOUND", [
            'lead_id' => $this->leadId
          ]);
          return;
        }
        Log::info('LEAD FOUND', [
        'email' => $lead->email,
        'product_id' => $lead->product_id,
        'source_id' => $lead->source_id
    ]);

    try {
      $template = $templateService->renderByProduct(
        $lead->product_id,
        $this->step,
        $this->channel,
        $this->data
      );
       Log::info('TEMPLATE RENDERED', [
            'subject' => $template['subject'] ?? null,
            'has_message' => !empty($template['message'])
        ]); 
    } catch (\Throwable $e) {
                   Log::info('TEMPLATE RENDERED', [
            'subject' => $template['subject'] ?? null,
            'has_message' => !empty($template['message'])
        ]);
        return;
    }
    try {
      Mail::raw($template['message'], function ($message) use ($template, $lead){
        $message->to($lead->email)
        ->subject($template['subject'] ?? 'Mensaje');
      });
       Log::info('MAIL SENT', [
            'to' => $lead->email
        ]);
    } catch (\Throwable $e) {
       Log::error('MAIL ERROR', [
            'error' => $e->getMessage()
        ]);
    }
    }
}
