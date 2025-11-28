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

        // Crear token con Sanctum
        $token = $user->createToken($dto->deviceName)->plainTextToken;

        return [
            'user' => $user->load('roles'), // Retornamos usuario con roles
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
        return $user->load('roles'); // Eager loading de roles
    }
}