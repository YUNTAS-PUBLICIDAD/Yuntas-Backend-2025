<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationLog extends Model
{
  protected $fillable = [
    'automation_execution_id',
    'template_step_id',
    'template_variant_id',
    'lead_id',
    'channel',
    'status',
    'response',
    'error',
    'sent_at'
  ];

  protected $casts = [
      'response' => 'array',
      'sent_at' => 'datetime',
  ];

  // =========================
  // RELATIONS
  // =========================
  public function execution(): BelongsTo
  {
    return $this->belongsTo(
    AutomationExecution::class,
    'automation_execution_id'
    );
  }

  public function step(): BelongsTo
  {
    return $this->belongsTo(
    TemplateStep::class,
    'template_step_id'
    );
  }

  public function variant(): BelongsTo
  {
    return $this->belongsTo(
    TemplateVariant::class,
    'template_variant_id'
    );
  }

  public function lead(): BelongsTo
  {
    return $this->belongsTo(
    Lead::class
    );
  }

}
