<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogContentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'slot_id',
        'text',
        'position',
    ];

    /**
     * Get the blog that owns the content item.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the slot that owns the content item.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(BlogContentSlot::class, 'slot_id');
    }
}
