<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogContentText extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'slot_id',
        'content',
    ];

    /**
     * Get the blog that owns the content text.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the slot that owns the content text.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(BlogContentSlot::class, 'slot_id');
    }
}
