<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class TrackingPageView extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'tracking_page_id',
        'session_id',
        'user_id',
        'ip_address',
        'viewed_at',
    ];
 
    protected $casts = [
        'viewed_at' => 'datetime',
    ];
 
    /**
     * Relación con la página.
     */
    public function trackingPage(): BelongsTo
    {
        return $this->belongsTo(TrackingPage::class, 'tracking_page_id');
    }
 
    /**
     * Relación con el usuario (si está autenticado).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
