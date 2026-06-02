<?php
 
namespace App\Domain\Repositories\PageView;
 
use App\Models\TrackingPageView;
 
interface TrackingPageViewRepositoryInterface
{
    /**
     * Guardar registro de vista.
     */
    public function save(array $data): TrackingPageView;
 
    /**
     * Buscar la última vista para cooldown.
     */
    public function findLastView(?string $sessionId, ?string $ipAddress, int $pageId): ?TrackingPageView;
 
    /**
     * Obtener páginas más vistas.
     */
    public function getMostViewedPages(?string $month = null): array;
 
    /**
     * Obtener estadísticas de tipos de usuario.
     */
    public function getUserTypeStats(): array;
}
