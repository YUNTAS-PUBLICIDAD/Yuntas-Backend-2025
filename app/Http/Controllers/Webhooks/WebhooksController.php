<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebhooksController extends Controller
{
    public function deployFrontend(Request $request)
    {
        $token = $request->header('X-GitHub-Token');
    
        if ($token !== env('WEBHOOK_TOKEN')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $status = $request->input('status', 'unknown');

        Cache::forget('rebuild_job_pending');
        
        if ($status === 'success') {
            Log::info('Deploy completado exitosamente');
        } else {
            Log::warning('Deploy falló o fue cancelado', ['status' => $status]);
            Cache::forget('frontend_needs_rebuild');
        }

        return response()->json([
            'message' => 'Webhook procesado correctamente'
        ]);
    }
}