<?php

namespace App\Domain\Repositories\Support;

use App\Models\Claim;
use App\Models\ClaimResponse;

interface ClaimRepositoryInterface
{
    public function getAll(int $perPage = 20);
    public function findById(int $id): ?Claim;
    public function findByCode(string $code): ?Claim; 
    public function save(array $data): Claim;
    public function updateStatus(int $id, int $statusId): bool;
    public function saveResponse(array $data): ClaimResponse;
}