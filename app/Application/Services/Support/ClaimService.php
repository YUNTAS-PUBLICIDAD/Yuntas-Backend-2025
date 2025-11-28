<?php

namespace App\Application\Services\Support;

use App\Application\DTOs\Support\CreateClaimDTO;
use App\Application\DTOs\Support\ReplyClaimDTO;
use App\Domain\Repositories\Support\ClaimRepositoryInterface;
use App\Models\Claim;
use App\Models\ClaimStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ClaimService
{
    public function __construct(
        private ClaimRepositoryInterface $repository
    ) {}

    public function getAll(int $perPage = 20)
    {
        return $this->repository->getAll($perPage);
    }

    public function getDetail(int $id)
    {
        $claim = $this->repository->findById($id);
        if (!$claim) throw new ModelNotFoundException("Reclamo no encontrado");
        return $claim;
    }

    public function create(CreateClaimDTO $dto): Claim
    {
        return DB::transaction(function () use ($dto) {
            // 1. Buscar ID del estado 'Pendiente' (o ID 1 por defecto)
            $status = ClaimStatus::where('name', 'Pendiente')->first();
            $statusId = $status ? $status->id : 1;

            // 2. Guardar Reclamo
            $claim = $this->repository->save([
                'first_name' => $dto->first_name,
                'last_name' => $dto->last_name,
                'document_type_id' => $dto->document_type_id,
                'document_number' => $dto->document_number,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'claim_type_id' => $dto->claim_type_id,
                'product_id' => $dto->product_id,
                'purchase_date' => $dto->purchase_date,
                'claimed_amount' => $dto->claimed_amount,
                'detail' => $dto->detail,
                'claim_status_id' => $statusId,
            ]);

            // 3. TODO: Enviar correo de confirmación al cliente ("Hemos recibido tu reclamo #ID")
            
            return $claim;
        });
    }

    public function reply(int $claimId, ReplyClaimDTO $dto)
    {
        return DB::transaction(function () use ($claimId, $dto) {
            $claim = $this->getDetail($claimId);

            // 1. Guardar respuesta
            $response = $this->repository->saveResponse([
                'claim_id' => $claim->id,
                'admin_id' => $dto->admin_id,
                'message' => $dto->message,
                'sent_via_email' => $dto->send_email,
                'email_sent_at' => $dto->send_email ? now() : null,
            ]);

            // 2. Actualizar estado a 'Atendido' (si existe ese estado)
            $statusAtendido = ClaimStatus::where('name', 'Atendido')->first();
            if ($statusAtendido) {
                $this->repository->updateStatus($claim->id, $statusAtendido->id);
            }

            // 3. TODO: Si $dto->send_email es true, enviar correo con la respuesta

            return $response;
        });
    }
}