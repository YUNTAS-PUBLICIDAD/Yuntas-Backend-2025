<?php

namespace App\Http\Controllers\Deploy;

use App\Http\Controllers\Controller;
use App\Application\Services\Deploy\DeployService;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    public function __construct(
        private DeployService $deployService
    ) {}

    public function trigger()
    {
        $result = $this->deployService->triggerDeploy();

        return response()->json($result, $result['success'] ? 200 : 409);
    }
}