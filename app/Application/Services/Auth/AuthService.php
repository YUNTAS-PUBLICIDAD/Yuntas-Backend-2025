<?php

namespace App\Application\Services\Auth;

use App\Application\DTOs\Auth\LoginDTO;
use App\Domain\Repositories\User\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}
    /**
     * Intenta loguear al usuario y devuelve el token.
     */
    public function login(LoginDTO $dto): array
    {
        $user = User::where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

   
        $token = $user->createToken($dto->deviceName)->plainTextToken;

        return [
            'user' => $user->load('role'), 
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    /**
     * Cierra la sesión (Revoca el token actual).
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Obtiene el perfil del usuario actual.
     */
    public function getProfile(User $user): User
    {
        return $user->load('role'); 
    }

    /**
     * Refresca el token de acceso (rota el token actual por uno nuevo).
     */
    public function refreshToken(string $bearerToken): array
    {
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($bearerToken);

        if (!$accessToken) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(
                401,
                'Token inválido o no encontrado.'
            );
        }

        $user = $accessToken->tokenable;
        $tokenName = $accessToken->name ?? 'API Token';

        // Eliminar el token viejo
        $accessToken->delete();

        // Crear uno nuevo
        $newToken = $user->createToken($tokenName)->plainTextToken;

        return [
            'user'  => $user->load('role'),
            'token' => $newToken,
            'token_type' => 'Bearer',
        ];
    }
}