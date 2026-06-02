<?php
 
namespace App\Infrastructure\Persistence\Eloquent\Repositories\PageView;
 
use App\Domain\Repositories\PageView\TrackingPageViewRepositoryInterface;
use App\Models\TrackingPageView;
use Illuminate\Support\Facades\DB;
 
class EloquentTrackingPageViewRepository implements TrackingPageViewRepositoryInterface
{
    /**
     * Guardar registro de vista.
     */
    public function save(array $data): TrackingPageView
    {
        return TrackingPageView::create($data);
    }
 
    /**
     * Buscar la última vista para cooldown.
     */
    public function findLastView(?string $sessionId, ?string $ipAddress, int $pageId): ?TrackingPageView
    {
        $query = TrackingPageView::where('tracking_page_id', $pageId);
 
        if ($sessionId) {
            $query->where('session_id', $sessionId);
        } elseif ($ipAddress) {
            $query->where('ip_address', $ipAddress);
        } else {
            return null;
        }
 
        return $query->orderBy('viewed_at', 'desc')->first();
    }
 
    /**
     * Obtener páginas más vistas.
     */
    public function getMostViewedPages(?string $month = null): array
    {
        $query = DB::table('tracking_page_views as tpv')
            ->join('tracking_pages as tp', 'tp.id', '=', 'tpv.tracking_page_id');

        if ($month) {
            [$year, $monthNumber] = explode('-', $month);
            $query->whereYear('tpv.viewed_at', $year)
                  ->whereMonth('tpv.viewed_at', $monthNumber);
        }

        return $query->select('tp.name', DB::raw('COUNT(tpv.id) AS total_views'))
            ->groupBy('tp.id', 'tp.name')
            ->orderByDesc('total_views')
            ->get()
            ->map(function ($row) {
                return [
                    'name' => $row->name,
                    'total_views' => (int) $row->total_views
                ];
            })
            ->toArray();
    }
 
    /**
     * Obtener estadísticas de tipos de usuario.
     */
    public function getUserTypeStats(): array
    {
        return DB::table('tracking_page_views')
            ->select(
                DB::raw("CASE WHEN user_id IS NULL THEN 'Anonimo' ELSE 'Registrado' END AS tipo_usuario"),
                DB::raw('COUNT(*) AS total_views')
            )
            ->groupBy('tipo_usuario')
            ->get()
            ->map(function ($row) {
                return [
                    'tipo_usuario' => $row->tipo_usuario,
                    'total_views' => (int) $row->total_views
                ];
            })
            ->toArray();
    }
}
