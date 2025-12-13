<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'cover_subtitle',
        'content',
        'status',
        'meta_title',
        'meta_description',
        'video_url',
    ];

    /**
     * Get the images for the blog.
     */
    public function images(): HasMany
    {
        return $this->hasMany(BlogImage::class);
    }

    /**
     * Get the content texts for the blog.
     */
    public function contentTexts(): HasMany
    {
        return $this->hasMany(BlogContentText::class);
    }

    /**
     * Get the content items for the blog.
     */
    public function contentItems(): HasMany
    {
        return $this->hasMany(BlogContentItem::class);
    }

    /**
     * Get the content blocks for the blog.
     */
    public function contentBlocks(): HasMany
    {
        return $this->hasMany(BlogContentBlock::class);
    }

    /**
     * Get the categories for the blog.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_blog');
    }
}
