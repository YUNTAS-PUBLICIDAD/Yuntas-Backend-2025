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

    protected static function booted()
    {
        static::deleting(function ($blog) {
            if (method_exists($blog, 'isForceDeleting') && !$blog->isForceDeleting()) {
                $blog->slug = $blog->slug . '-deleted-' . time();
                $blog->saveQuietly();
            }
        });
    }

    protected $fillable = [
        'title',
        'slug',
        'hero_title',
        'cover_subtitle',
        'status',
        'meta_title',
        'meta_description',
        'video_url',
        'keywords',

        'product_id',
    ];

    protected $casts = [
        'keywords' => 'array',
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
