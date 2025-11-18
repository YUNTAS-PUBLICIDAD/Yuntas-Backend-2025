<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogContentSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'data_type',
        'position',
    ];

    /**
     * Get the content texts for the slot.
     */
    public function contentTexts(): HasMany
    {
        return $this->hasMany(BlogContentText::class, 'slot_id');
    }

    /**
     * Get the content items for the slot.
     */
    public function contentItems(): HasMany
    {
        return $this->hasMany(BlogContentItem::class, 'slot_id');
    }

    /**
     * Get the content blocks for the slot.
     */
    public function contentBlocks(): HasMany
    {
        return $this->hasMany(BlogContentBlock::class, 'slot_id');
    }
}
