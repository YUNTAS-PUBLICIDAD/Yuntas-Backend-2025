<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImageSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'module',
        'name',
        'description',
        'position',
    ];

    /**
     * Get the product images for the slot.
     */
    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'slot_id');
    }

    /**
     * Get the blog images for the slot.
     */
    public function blogImages(): HasMany
    {
        return $this->hasMany(BlogImage::class, 'slot_id');
    }
}
