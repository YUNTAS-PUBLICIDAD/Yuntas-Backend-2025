<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!$request->user() || !$request->user()->role) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $userRole = $request->user()->role->name;
        $allowedRoles = explode('|', $roles);

        // Si el usuario no tiene ninguno de los roles permitidos
        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        // Si es marketing o ventas, solo puede hacer GET
        if (in_array($userRole, ['marketing', 'ventas'])) {
            if (!$request->isMethod('get')) {
                return response()->json([
                    'message' => 'Tu rol solo permite consultar información.'
                ], 403);
            }
        }

        return $next($request);
    }
    
}
