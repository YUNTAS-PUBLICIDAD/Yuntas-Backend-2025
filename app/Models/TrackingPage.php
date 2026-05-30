<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class TrackingPage extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'route',
        'name',
    ];
 
    /**
     * Relación con las vistas de página.
     */
    public function trackingPageViews(): HasMany
    {
        return $this->hasMany(TrackingPageView::class, 'tracking_page_id');
    }
}
