<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InicioMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $cliente;

    public function __construct(array $cliente)
    {
        $this->cliente = $cliente;
    }

    public function build()
    {
        return $this->subject('Bienvenido(a) a Yuntas Publicidad ✨')
            ->view('emails.inicio-mailing')
            ->with(['cliente' => $this->cliente]);
    }
}