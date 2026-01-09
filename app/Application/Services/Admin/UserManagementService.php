<?php

namespace App\Application\Services\Admin;

use App\Application\DTOs\Admin\UserDTO;
use App\Domain\Repositories\User\UserRepositoryInterface;
use App\Models\Role; 
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
            
           
            $roleId = $dto->role_id ?? 2; 

            $data = [
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
                'role_id' => $roleId, 
            ];

            return $this->repository->create($data);
        });
    }

   
    public function update(int $id, UserDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $user = $this->getById($id);

            $data = [
                'name' => $dto->name,
                'email' => $dto->email,
            ];

            if (!empty($dto->password)) {
                $data['password'] = Hash::make($dto->password);
            }

            
            if ($dto->role_id) {
                $data['role_id'] = $dto->role_id;
            }

            $this->repository->update($id, $data);

            return $user->fresh('role'); 
        });
    }

    public function delete(int $id): void
    {
        $this->getById($id); 
        $this->repository->delete($id);
    }

    public function assignRole(int $userId, string $roleName)
    {
        $user = $this->getById($userId);
        $role = Role::where('name', $roleName)->first();

        if ($role) {
            $this->repository->update($userId, ['role_id' => $role->id]);
        }

        return $user->fresh('role');
    }
}