<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductContentSlot extends Model
{
    use HasFactory;
    protected $table = 'product_content_slots';
    protected $fillable = [
        'name',
        'data_type',
        'position',
    ];

    /**
     * Get the content items for the slot.
     */
    public function contentItems(): HasMany
    {
        return $this->hasMany(ProductContentItem::class, 'slot_id');
    }

    /**
     * Get the content texts for the slot.
     */
    public function contentTexts(): HasMany
    {
        return $this->hasMany(ProductContentText::class, 'slot_id');
    }
}
