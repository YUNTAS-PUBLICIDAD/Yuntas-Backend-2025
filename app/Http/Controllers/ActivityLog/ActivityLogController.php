<?php

namespace App\Http\Controllers\ActivityLog;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\Services\ActivityLog\ActivityLogService;
use App\Http\Resources\ActivityLog\ActivityLogResource;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $service
    ) {}

    /**
     * Obtener el historial de actividades.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $logs = $this->service->getAll(
                $request->get('perPage', 10)
            );

            return response()->json([
                'success' => true,
                'data' => ActivityLogResource::collection($logs)
                    ->response()
                    ->getData(true)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}