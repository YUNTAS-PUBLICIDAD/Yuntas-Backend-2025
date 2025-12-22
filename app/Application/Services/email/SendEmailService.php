<?php

namespace App\Application\Services\Email;

use App\Application\DTOs\email\SendEmailDTO;
use App\Domain\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Mail\Mailer;
use App\Mail\ProductSlotsMailable;

class SendEmailService
{
    private ProductRepositoryInterface $productRepo;
    private Mailer $mailer;

    public function __construct(ProductRepositoryInterface $productRepo, Mailer $mailer)
    {
        $this->productRepo = $productRepo;
        $this->mailer = $mailer;
    }

    public function handle(SendEmailDTO $dto): array
    {
        // 1. Obtener producto
        $product = $this->productRepo->findById($dto->productId);
        if (!$product) {
            return ['message' => 'Product not found', 'sent' => false];
        }

        // 2. Preparar slots base desde el producto
        $defaultSlots = [
            'product_name' => $product->name,
            'product_price' => $product->price,
            'product_description' => $product->description,
            'product_url' => url("/products/{$product->id}"),
        ];

        // 3. Merge con custom slots que envió el cliente
        $slots = array_merge($defaultSlots, $dto->customSlots);

        // 4. Obtener template (puedes guardar templates en DB o filesystem)
        $template = $this->getTemplate($dto->templateId);

        // 5. Renderizar body reemplazando placeholders
        $body = $this->renderSlots($template['body'], $slots);
        $subject = $this->renderSlots($template['subject'], $slots);

        // 6. Enviar correo (Mailable)
        $mailable = new ProductSlotsMailable($subject, $body);

        $this->mailer->to($dto->recipientEmail)->send($mailable);

        return ['message' => 'Email sent', 'sent' => true];
    }

    private function getTemplate(?string $templateId): array
    {
        // ejemplo simple. Puedes cargar de DB o archivos.
        if ($templateId === 'promo') {
            return [
                'subject' => 'Oferta: [[product_name]] - ¡solo por S/ [[product_price]]!',
                'body' => "<h1>[[product_name]]</h1><p>[[product_description]]</p><p>Precio: [[product_price]]</p><a href='[[product_url]]'>Ver producto</a>"
            ];
        }

        // template por defecto
        return [
            'subject' => 'Información del producto: [[product_name]]',
            'body' => "<h1>[[product_name]]</h1><p>[[product_description]]</p><p>Precio: [[product_price]]</p><a href='[[product_url]]'>Ver producto</a>"
        ];
    }

    private function renderSlots(string $templateString, array $slots): string
    {
        // Reemplaza [[key]] por value
        $result = $templateString;
        foreach ($slots as $key => $value) {
            $result = str_replace("[[$key]]", e($value), $result);
        }
        return $result;
    }
}
