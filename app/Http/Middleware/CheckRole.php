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
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || ! $request->user()->role) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        if ($request->user()->role->name !== $role) {
            return response()->json(['message' => 'No tienes permisos de Administrador para realizar esta acción.'], 403);
        }

        return $next($request);
    }
    
}
