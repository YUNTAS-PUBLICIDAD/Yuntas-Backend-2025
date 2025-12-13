<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_id',
        'admin_id',
        'message',
        'sent_via_email',
        'email_sent_at',
    ];

    protected $casts = [
        'sent_via_email' => 'boolean',
        'email_sent_at' => 'datetime',
    ];

    /**
     * Get the claim that owns the response.
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    /**
     * Get the admin that created the response.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
