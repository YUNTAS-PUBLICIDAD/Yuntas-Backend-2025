<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\Services\Support\RoleService;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $roles = $this->service->getAll($request->get('perPage', 20));
        return response()->json(['success' => true, 'data' => $roles]);
    }
}