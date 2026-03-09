<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories\Support;

use App\Domain\Repositories\Support\ContactRepositoryInterface;
use App\Models\ContactMessage;

class EloquentContactRepository implements ContactRepositoryInterface
{
    public function getAll(int $perPage = 20)
    {
        return ContactMessage::latest()->paginate($perPage);
    }

    public function findById(int $id): ?ContactMessage
    {
        return ContactMessage::find($id);
    }

    public function save(array $data): ContactMessage
    {
        return ContactMessage::create($data);
    }

    public function update(array $data, int $id): ContactMessage
    {
        $contact = ContactMessage::findOrFail($id);
        $contact->update($data);
        return $contact->fresh();
    }

    public function delete(int $id): bool
    {
        return ContactMessage::destroy($id) > 0;
    }
}