<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationAction extends Model
{
    use HasFactory;

    protected $fillable = [
    'automation_rule_id',
    'template_variant_id',
    'priority',
    'delay_seconds',
    'conditions'
    ];

    protected $casts = [
      'delay_seconds' => 'integer',
      'conditions' => 'array',
    ];

    public function rule()
    {
      return $this->belongsTo(AutomationRule::class);
    }

    public function variant()
    {
      return $this->belongsTo(TemplateVariant::class);
    }
}
