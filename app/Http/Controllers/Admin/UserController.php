<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\Services\Admin\UserManagementService;
use App\Application\DTOs\Admin\UserDTO;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct(
        private UserManagementService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $users = $this->service->getAll($request->get('perPage', 10));
        return response()->json(['success' => true, 'data' => $users]);
    }

    public function show($id): JsonResponse
    {
        try {
            $user = $this->service->getById($id);
            return response()->json(['success' => true, 'data' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $dto = UserDTO::fromRequest($request);
            $user = $this->service->create($dto);
            
            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        try {
            $dto = UserDTO::fromRequest($request);
            $user = $this->service->update($id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function assignRole(Request $request, $id): JsonResponse
    {
        $request->validate(['role' => 'required|string']);
        try {
            $user = $this->service->assignRole($id, $request->role);
            return response()->json(['success' => true, 'message' => 'Rol asignado', 'data' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}