<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateAsset extends Model
{
    use HasFactory;

    protected $fillable = [
      'template_variant_id',
      'key',
      'path',
      'meta'
    ];

    protected $casts = [
      'meta' => 'array'
    ];

    public function variant()
    {
      return $this->belongsTo(TemplateVariant::class);
    }
}
