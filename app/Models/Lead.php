<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'product_id',
        'source_id',
    ];

    /**
     * Get the product that the lead is interested in.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the source of the lead.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    /**
     * Get the WhatsApp messages for the lead.
     */
    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class);
    }

    /**
     * Get the email messages for the lead.
     */
    public function emailMessages(): HasMany
    {
        return $this->hasMany(EmailMessage::class);
    }

    /**
     * Get the message logs for the lead.
     */
    public function messageLogs(): HasMany
    {
        return $this->hasMany(MessageLog::class);
    }
}
