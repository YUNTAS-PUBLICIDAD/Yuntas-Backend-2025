<?php

namespace App\Application\Services;

use App\Application\DTOs\Support\SendEmailDTO;
use App\Domain\Repositories\Support\EmailRepositoryInterface;


//nuevo
use App\Domain\Repositories\Product\ProductRepositoryInterface;


class SendEmailService
{
    public function __construct(
        private EmailRepositoryInterface $repository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function send(SendEmailDTO $dto): bool
    {
        // Obtener productos seleccionados
        $productos = [];
        foreach ($dto->productos as $id) {
            $producto = $this->productRepository->findById($id);
            if ($producto) {
                $productos[] = $producto;
            }
        }

        // Armar HTML del mensaje
        $lista = "<h2>Productos seleccionados</h2><ul>";

        foreach ($productos as $p) {
            $lista .= "<li><strong>{$p->name}</strong> — S/ {$p->price}</li>";
        }

        $lista .= "</ul>";

        // Si había message previo, lo concatenamos
        $dto->message = ($dto->message ?: '') . $lista;

        return $this->repository->send($dto);
    }
}
