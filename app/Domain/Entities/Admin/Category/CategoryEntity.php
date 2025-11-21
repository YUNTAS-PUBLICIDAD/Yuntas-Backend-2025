<?php

namespace App\Domain\Entities\Admin\Category;

class CategoryEntity
{
    public int $id;
    public string $name;
    public ?string $description;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        int $id,
        string $name,
        ?string $description = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
}
