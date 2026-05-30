<?php
 
namespace App\Application\DTOs\PageView;
 
use Illuminate\Http\Request;
 
class TrackingPageViewDTO
{
    public function __construct(
        public readonly string $route,
        public readonly string $session_id,
        public readonly ?int $user_id,
        public readonly ?string $ip_address
    ) {}
 
    /**
     * Crear DTO desde una petición HTTP.
     */
    public static function fromRequest(Request $request, ?string $sessionId = null): self
    {
        $route = $request->input('route');
 
        // Normalizar la ruta para quedarse solo con el path (ej. /nosotros)
        $parsed = parse_url($route, PHP_URL_PATH) ?? '/';
        $normalized = '/' . trim($parsed, '/');
 
        // Resolver manualmente o desde el guard de Sanctum el usuario autenticado
        $userId = $request->user('sanctum')?->id;
        if (!$userId && ($token = $request->bearerToken())) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable) {
                $userId = $accessToken->tokenable->id;
            }
        }
 
        return new self(
            route: $normalized,
            session_id: $sessionId ?? $request->input('session_id') ?? '',
            user_id: $userId,
            ip_address: $request->ip()
        );
    }
}
