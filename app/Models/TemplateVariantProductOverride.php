<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateVariantProductOverride extends Model
{
  protected $fillable = [
    'template_variant_id',
    'product_id',
    'subject',
    'content',
    'cta_text',
    'cta_url',
    'variables',
    'assets',
    'active'
  ];

  protected $casts = [
  'variables' => 'array',
  'assets' => 'array',
  'active' => 'boolean',
  ];

  // =========================
  // RELATIONS
  // =========================
  public function variant(): BelongsTo
  {
    return $this->belongsTo(
    TemplateVariant::class,
    'template_variant_id'
    );
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(
    Product::class
    );
  }
}
