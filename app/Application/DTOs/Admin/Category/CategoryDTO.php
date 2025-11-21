<?php

namespace App\Application\DTOs\Admin\Category;

class CategoryDTO
{
    public string $name;
    public ?string $description;

    public function __construct(string $name, ?string $description = null)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['description'] ?? null
        );
    }
}
