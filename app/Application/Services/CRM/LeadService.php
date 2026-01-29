<?php

namespace App\Application\Services\CRM;

use App\Application\DTOs\CRM\LeadDTO;
use App\Domain\Repositories\CRM\LeadRepositoryInterface;
use App\Models\Lead;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\DB;

class LeadService
{
    public function __construct(
        private LeadRepositoryInterface $repository
    ) {}

    public function create(LeadDTO $dto): Lead
    {
        return DB::transaction(function () use ($dto) {
            $lead = $this->repository->save([
                'name' => $dto->name,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'message' => $dto->message,
                'product_id' => $dto->product_id,
                'source_id' => $dto->source_id,
            ]);


            return $lead;
        });
    }

    public function getAll(int $perPage = 20)
    {
        return $this->repository->getAll($perPage);
    }

    public function update(int $id, LeadDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $lead = Lead::findOrFail($id);

            // Se detecta si phone cambió
            $phoneChanged = $lead->phone !== $dto->phone;

            $lead->update([
                'name' => $dto->name,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'message' => $dto->message,
                'product_id' => $dto->product_id,
                'source_id' => $dto->source_id,
            ]);

            if ($phoneChanged) { // si cambió el teléfono, limpiar chat_id en mensajes de WhatsApp
                WhatsappMessage::where('lead_id', $lead->id)
                    ->whereNotNull('chat_id')
                    ->update(['chat_id' => null]);
            }

            return $lead->refresh();
        });
    }

    public function delete(int $id): void
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();
    }
}