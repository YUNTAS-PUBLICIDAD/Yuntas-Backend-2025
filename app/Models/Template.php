<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
  use HasActivityLog;
  protected $fillable = [
    // 'lead_source_id',
    'name',
    'context',
    'active',
  ];


  // public function variants()
// {
//   return $this->hasMany(TemplateVariant::class);
// }

  public function steps(): HasMany
  {
    return $this->hasMany(TemplateStep::class)
      ->orderBy('step');
  }

  public function executions(): HasMany
  {
    return $this->hasMany(
      AutomationExecution::class
    );
  }
}
