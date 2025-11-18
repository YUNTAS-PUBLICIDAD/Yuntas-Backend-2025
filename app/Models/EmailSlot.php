<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'position',
    ];

    /**
     * Get the images for the slot.
     */
    public function images(): HasMany
    {
        return $this->hasMany(EmailImage::class, 'slot_id');
    }

    /**
     * Get the messages for the slot.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(EmailMessage::class, 'slot_id');
    }
}
