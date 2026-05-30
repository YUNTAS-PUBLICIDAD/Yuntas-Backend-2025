<?php
 
namespace App\Http\Controllers\PageView;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\PageView\StoreTrackingPageViewRequest;
use App\Application\DTOs\PageView\TrackingPageViewDTO;
use App\Application\Services\PageView\TrackingService;
use Illuminate\Http\JsonResponse;
 
/**
 * @OA\Tag(
 *     name="PageView Tracking",
 *     description="Endpoints para registro de visitas y estadísticas de páginas"
 * )
 */
class TrackingController extends Controller
{
    public function __construct(
        private TrackingService $trackingService
    ) {}
 
    /**
     * @OA\Post(
     *     path="/api/page-view",
     *     summary="Registrar una vista de página",
     *     tags={"PageView Tracking"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"route", "session_id"},
     *             @OA\Property(property="route", type="string", example="/productos"),
     *             @OA\Property(property="session_id", type="string", format="uuid", example="9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Vista registrada"),
     *     @OA\Response(response=200, description="Vista omitida o ignorada"),
     *     @OA\Response(response=500, description="Error interno")
     * )
     */
    public function store(StoreTrackingPageViewRequest $request): JsonResponse
    {
        try {
            $sessionId = $request->input('session_id') ?: (string) \Illuminate\Support\Str::uuid();
            $dto = TrackingPageViewDTO::fromRequest($request, $sessionId);
            $result = $this->trackingService->store($dto);
 
            switch ($result['status']) {
                case 'ignored_admin':
                    return response()->json([
                        'success' => true,
                        'session_id' => $sessionId,
                        'message' => 'Visita de administrador ignorada (no se registra).'
                    ], 200);
 
                case 'ignored_not_monitored':
                    return response()->json([
                        'success' => true,
                        'session_id' => $sessionId,
                        'message' => 'Página no monitoreada para tracking.'
                    ], 200);
 
                case 'cooldown':
                    return response()->json([
                        'success' => true,
                        'session_id' => $sessionId,
                        'message' => 'Vista omitida por cooldown de 15 segundos.'
                    ], 200);
 
                case 'success':
                default:
                    return response()->json([
                        'success' => true,
                        'session_id' => $sessionId,
                        'message' => 'Vista de página registrada exitosamente.'
                    ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar vista: ' . $e->getMessage()
            ], 500);
        }
    }
 
    /**
     * @OA\Get(
     *     path="/api/dashboard/most-viewed-pages",
     *     summary="Obtener las páginas más vistas",
     *     tags={"PageView Tracking"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Estadísticas de páginas más vistas")
     * )
     */
    public function mostViewedPages(): JsonResponse
    {
        try {
            $stats = $this->trackingService->getMostViewedPages();
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
 
    /**
     * @OA\Get(
     *     path="/api/dashboard/user-type-stats",
     *     summary="Obtener estadísticas por tipo de usuario",
     *     tags={"PageView Tracking"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Distribución por tipo de usuario")
     * )
     */
    public function userTypeStats(): JsonResponse
    {
        try {
            $stats = $this->trackingService->getUserTypeStats();
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
