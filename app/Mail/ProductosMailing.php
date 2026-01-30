<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductosMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $cliente;

    public function __construct(array $cliente)
    {
        $this->cliente = $cliente;
    }

    public function build()
    {
        return $this->subject('Gracias por contactarnos')
            ->view('emails.productos-mailing')
            ->with(['cliente' => $this->cliente]);
    }
}