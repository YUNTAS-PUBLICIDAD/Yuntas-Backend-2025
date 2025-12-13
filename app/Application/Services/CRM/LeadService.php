<?php

namespace App\Application\Services\CRM;

use App\Application\DTOs\CRM\LeadDTO;
use App\Domain\Repositories\CRM\LeadRepositoryInterface;
use App\Models\Lead;
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
}