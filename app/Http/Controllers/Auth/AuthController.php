<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\Services\Auth\AuthService;
use App\Application\DTOs\Auth\LoginDTO;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = LoginDTO::fromRequest($request);
            $data = $this->authService->login($dto);

            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => $data
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de autenticación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->getProfile($request->user());

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se proporcionó un token.'
                ], 401);
            }

            $data = $this->authService->refreshToken($token);

            return response()->json([
                'success' => true,
                'message' => 'Token refrescado exitosamente',
                'data' => $data
            ]);

        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al refrescar el token.'
            ], 500);
        }
    }
}