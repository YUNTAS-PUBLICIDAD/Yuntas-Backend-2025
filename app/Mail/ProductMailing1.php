<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailProducto;

class ProductMailing1 extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $cliente;
    protected $viewName;

    public function __construct(EmailProducto $seccion, array $cliente)
    {
        $imagenes = is_string($seccion->imagenes_secundarias)
            ? json_decode($seccion->imagenes_secundarias, true)
            : [];

        $this->data = [
            'titulo' => $seccion->titulo,
            'paso' => $seccion->paso,
            'parrafo1' => $seccion->parrafo1,
            'imagen_principal' => EmailProducto::buildImageUrl($seccion->imagen_principal),
            'imagenes_secundarias' => array_map(
                fn ($img) => EmailProducto::buildImageUrl($img),
                $imagenes
            ),
        ];

        $this->cliente = $cliente;

        // 👇 seleccionar vista según paso
        $this->viewName = match ($seccion->paso) {
            0 => 'emails.producto-generico',
            1 => 'emails.producto-generico2',
            2 => 'emails.producto-generico3',
            default => 'emails.producto-generico',
        };
    }

    public function build()
    {
        return $this->subject($this->data['titulo'])
            ->view($this->viewName)
            ->with([
                'data' => $this->data,
                'cliente' => $this->cliente,
            ]);
    }
}









