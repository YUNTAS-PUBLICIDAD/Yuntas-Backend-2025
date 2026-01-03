<?php

namespace App\Domain\Repositories\CRM;

use App\Models\Lead;

interface LeadRepositoryInterface
{
    public function getAll(int $perPage = 20);
    public function save(array $data): Lead;
    public function findByEmail(string $email): ?Lead;
    public function findById(int $id): ?Lead;
}