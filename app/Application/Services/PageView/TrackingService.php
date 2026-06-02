<?php
 
namespace App\Application\Services\PageView;
 
use App\Application\DTOs\PageView\TrackingPageViewDTO;
use App\Domain\Repositories\PageView\TrackingPageViewRepositoryInterface;
use App\Models\TrackingPage;
 
class TrackingService
{
    public function __construct(
        private TrackingPageViewRepositoryInterface $pageViewRepository
    ) {}
 
    /**
     * Registrar una visita de página.
     */
    public function store(TrackingPageViewDTO $dto): array
    {
        // 1. Si el usuario está autenticado (es admin/staff), se ignora el tracking
        if ($dto->user_id !== null) {
            return ['status' => 'ignored_admin'];
        }
 
        // 2. Verificar si la ruta está registrada en las páginas oficiales a monitorear
        $page = TrackingPage::where('route', $dto->route)->first();
        if (!$page) {
            return ['status' => 'ignored_not_monitored'];
        }
 
        // 3. Control de Cooldown: Evitar doble contabilización dentro de un rango de 15 segundos
        $lastView = $this->pageViewRepository->findLastView($dto->session_id, $dto->ip_address, $page->id);
        if ($lastView && now()->diffInSeconds($lastView->viewed_at) < 15) {
            return [
                'status' => 'cooldown',
                'page_view' => $lastView
            ];
        }
 
        // 4. Registrar la visita
        $pageView = $this->pageViewRepository->save([
            'tracking_page_id' => $page->id,
            'session_id' => $dto->session_id,
            'user_id' => $dto->user_id,
            'ip_address' => $dto->ip_address,
            'viewed_at' => now(),
        ]);
 
        return [
            'status' => 'success',
            'page_view' => $pageView
        ];
    }
 
    /**
     * Obtener estadísticas de páginas más vistas.
     */
    public function getMostViewedPages(?string $month = null): array
    {
        return $this->pageViewRepository->getMostViewedPages($month);
    }
 
    /**
     * Obtener estadísticas por tipo de usuario.
     */
    public function getUserTypeStats(): array
    {
        return $this->pageViewRepository->getUserTypeStats();
    }
}
