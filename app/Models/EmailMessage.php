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
        'type',
        'subject',
        'body',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Get the lead that owns the email message.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function scopePopup($query)
    {
        return $query->where('type', 'popup');
    }

    public function scopeCampaign($query)
    {
        return $query->where('type', 'campaign');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'enviado');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'fallido');
    }

    public function product()
    {
        return $this->belongsTo(EmailProduct::class);
    }
}
