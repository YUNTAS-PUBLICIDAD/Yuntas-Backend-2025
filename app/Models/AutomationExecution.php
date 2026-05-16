<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationExecution extends Model
{
  protected $fillable = [
    'template_id',
    'lead_id',
    'current_step',
    'status',
    'next_run_at',
    'started_at',
    'finished_at'
  ];

  protected $casts = [
    'next_run_at' => 'datetime',
    'started_at' => 'datetime',
    'finished_at' => 'datetime'
  ];

  // =========================
  // RELATIONS
  // =========================
  public function template(): BelongsTo
  {
    return $this->belongsTo(
        Template::class
    );
  }

  public function lead(): BelongsTo
  {
    return $this->belongsTo(
    Lead::class
    );
  }

  public function logs(): HasMany
  {
    return $this->hasMany(
    AutomationLog::class
    );
  }
}
