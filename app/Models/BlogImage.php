<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'slot_id',
        'url',
    ];

    /**
     * Get the blog that owns the image.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the slot that owns the image.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(ImageSlot::class, 'slot_id');
    }
}
