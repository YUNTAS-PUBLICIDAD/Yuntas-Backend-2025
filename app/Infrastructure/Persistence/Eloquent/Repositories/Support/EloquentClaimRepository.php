<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Support;

use App\Domain\Repositories\Support\ClaimRepositoryInterface;
use App\Models\Claim;
use App\Models\ClaimResponse;

class EloquentClaimRepository implements ClaimRepositoryInterface
{
    public function getAll(int $perPage = 20)
    {
        return Claim::with(['claimStatus', 'claimType', 'documentType'])
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Claim
    {
        return Claim::with(['responses.admin', 'product', 'claimStatus'])->find($id);
    }

    public function findByCode(string $code): ?Claim
    {
        return Claim::where('document_number', $code)->first();
    }

    public function save(array $data): Claim
    {
        return Claim::create($data);
    }

    public function updateStatus(int $id, int $statusId): bool
    {
        return Claim::where('id', $id)->update(['claim_status_id' => $statusId]);
    }

    public function saveResponse(array $data): ClaimResponse
    {
        return ClaimResponse::create($data);
    }
}