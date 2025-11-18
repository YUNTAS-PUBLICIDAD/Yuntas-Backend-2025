<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductContentText extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'slot_id',
        'content',
    ];

    /**
     * Get the product that owns the content text.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the slot that owns the content text.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(ProductContentSlot::class, 'slot_id');
    }
}
