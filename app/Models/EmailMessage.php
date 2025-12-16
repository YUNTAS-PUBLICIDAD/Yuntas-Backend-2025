<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'slot_id',
        'subject',
        'body',
        'status',
        'scheduled_at',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the lead that owns the email message.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the slot for the email message.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(EmailSlot::class, 'slot_id');
    }

    public function slots()
{
    return $this->hasMany(EmailSlot::class);
}

public function product()
{
    return $this->belongsTo(EmailProduct::class);
}



}
