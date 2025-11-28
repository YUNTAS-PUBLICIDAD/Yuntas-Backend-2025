<?php

namespace App\Application\Services\Admin;

use App\Application\DTOs\Admin\UserDTO;
use App\Domain\Repositories\User\UserRepositoryInterface; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserManagementService
{
    public function __construct(
        private UserRepositoryInterface $repository 
    ) {}

    public function getAll(int $perPage = 10)
    {
        return $this->repository->getAll($perPage);
    }

    public function getById(int $id)
    {
        $user = $this->repository->findById($id);
        if (!$user) {
            throw new ModelNotFoundException("Usuario no encontrado");
        }
        return $user;
    }

    public function create(UserDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $data = [
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
            ];

            $user = $this->repository->create($data);

            // Asignación de Rol (Spatie)
            $roleToAssign = $dto->role ?? 'user';
            if (method_exists($user, 'assignRole')) {
                $user->assignRole($roleToAssign);
            }

            return $user;
        });
    }

    public function update(int $id, UserDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            // Verificamos existencia
            $user = $this->getById($id);

            $data = [
                'name' => $dto->name,
                'email' => $dto->email,
            ];

            if (!empty($dto->password)) {
                $data['password'] = Hash::make($dto->password);
            }

            // Actualizamos usando el repositorio
            $this->repository->update($id, $data);

            if ($dto->role && method_exists($user, 'syncRoles')) {
                $user->syncRoles([$dto->role]);
            }

            return $user->fresh('roles');
        });
    }

    public function delete(int $id): void
    {
        $this->getById($id); // Verificamos existencia
        $this->repository->delete($id);
    }

    public function assignRole(int $userId, string $roleName)
    {
        $user = $this->getById($userId);
        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$roleName]);
        }
        return $user->fresh('roles');
    }
}