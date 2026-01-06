<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\CRM;

use App\Domain\Repositories\CRM\LeadRepositoryInterface;
use App\Models\Lead;

class EloquentLeadRepository implements LeadRepositoryInterface
{
    public function getAll(int $perPage = 20)
    {
        // Cargamos el producto y la fuente para mostrar en el listado admin
        return Lead::with(['product', 'source'])->latest()->paginate($perPage);
    }

    public function save(array $data): Lead
    {
        $lead = Lead::create($data);
        return $lead->load('product', 'source');
    }

    public function findByEmail(string $email): ?Lead
    {
        return Lead::with(['product', 'source'])->where('email', $email)->first();
    }

    public function findById(int $id): ?Lead
    {
        return Lead::with(['product', 'source'])->find($id);
    }
}