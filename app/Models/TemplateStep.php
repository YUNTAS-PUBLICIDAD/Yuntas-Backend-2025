<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateStep extends Model
{
  protected $fillable = [
      'template_id',
      'step',
      'delay_value',
      'delay_unit',
      'active'
  ];

  protected $casts = [
    'active' => 'boolean'
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

  public function variants(): HasMany
  {
    return $this->hasMany(
    TemplateVariant::class
    );
  }

  // =========================
  // HELPERS
  // =========================
  public function getDelayLabelAttribute(): string
  {
     return "{$this->delay_value} {$this->delay_unit}";
  }
}
