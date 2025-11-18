<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductContentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'slot_id',
        'text',
        'position',
    ];

    /**
     * Get the product that owns the content item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the slot that owns the content item.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(ProductContentSlot::class, 'slot_id');
    }
}
