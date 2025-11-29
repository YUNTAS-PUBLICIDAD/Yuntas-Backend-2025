<?php

namespace App\Application\Services\Support;

use App\Application\DTOs\Support\CreateContactDTO;
use App\Domain\Repositories\Support\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContactService
{
    public function __construct(
        private ContactRepositoryInterface $repository
    ) {}

    public function getAll(int $perPage = 20)
    {
        return $this->repository->getAll($perPage);
    }

    public function getById(int $id)
    {
        $message = $this->repository->findById($id);
        if (!$message) {
            throw new ModelNotFoundException("Mensaje de contacto no encontrado");
        }
        return $message;
    }

    public function create(CreateContactDTO $dto)
    {
        return $this->repository->save([
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'phone' => $dto->phone,
            'district' => $dto->district,
            'request_detail' => $dto->request_detail,
            'message' => $dto->message,
        ]);
    }

    public function delete(int $id): void
    {
        $this->getById($id); 
        $this->repository->delete($id);
    }
}