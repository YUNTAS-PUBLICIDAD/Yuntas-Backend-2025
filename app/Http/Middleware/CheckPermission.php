<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        // Usuario no autenticado
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.'
            ], 401);
        }

        // Cargar rol y permisos
        $user->loadMissing('role.permissions');

        // Usuario sin rol
        if (!$user->role) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene un rol asignado.'
            ], 403);
        }

        // Verificar permiso
        $hasPermission = $user->role
            ->permissions
            ->contains('name', $permission);

        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        return $next($request);
    }
}