<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTemplateAsset extends Model
{
    use HasFactory;

    protected $fillable = [
      'product_id',
      'template_variant_id',
      'key',
      'path'
    ];

    protected $casts = [
      'product_id' => 'integer',
      'template_variant_id' => 'integer',
    ];

    // opcional (solo si realmente lo necesitas)
    public function product()
    {
      return $this->belongsTo(Product::class);
    }

    public function variant()
    {
      return $this->belongsTo(TemplateVariant::class, 'template_variant_id');
    }
}
