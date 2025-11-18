<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_id',
        'url',
    ];

    /**
     * Get the slot that owns the image.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(EmailSlot::class, 'slot_id');
    }
}
