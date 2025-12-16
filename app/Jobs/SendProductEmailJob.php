<?php

namespace App\Jobs;

use App\Models\EmailProducto;
use App\Mail\ProductMailing1;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Log;

use App\Mail\ProductMailingInfo;

class SendProductEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productoId;
    protected $paso;
    protected $cliente;

    public function __construct(int $productoId, int $paso, array $cliente)
    {
        $this->productoId = $productoId;
        $this->paso = $paso;
        $this->cliente = $cliente;
    }

   public function handle()
{
    $seccion = EmailProducto::where('producto_id', $this->productoId)
        ->where('paso', $this->paso)
        ->first();

    if (!$seccion) {
        Log::warning('No existe plantilla', [
            'producto_id' => $this->productoId,
            'paso' => $this->paso
        ]);
        return;
    }

    Mail::to($this->cliente['correo'])
        ->send(new ProductMailing1($seccion, $this->cliente));
}
}