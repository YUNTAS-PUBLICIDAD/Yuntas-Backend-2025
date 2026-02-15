<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Application\Services\Deploy\DeployService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhooksController extends Controller
{
    public function __construct(
        private DeployService $deployService
    ) {}

    public function deployFrontend(Request $request)
    {
        $token = $request->header('X-GitHub-Token');
    
        if ($token !== env('WEBHOOK_TOKEN')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $status = $request->input('status', 'unknown');

        $this->deployService->releaseDeployLock(); 
        
        if ($status === 'success') {
            Log::info('Deploy completado exitosamente');
        } else {
            Log::warning('Deploy falló o fue cancelado', ['status' => $status]);
        }

        return response()->json([
            'message' => 'Webhook procesado correctamente',
            'status' => $status
        ]);
    }
}