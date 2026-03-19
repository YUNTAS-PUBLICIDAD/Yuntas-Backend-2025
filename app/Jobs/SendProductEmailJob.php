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
use App\Models\EmailMessage;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;



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

    // $lead = Lead::where('email', $this->cliente['correo'])->first();
    $lead = Lead::where('email', $this->cliente['email'])->first();

    try {
        // Mail::to($this->cliente['correo'])
        Mail::to($this->cliente['email'])
            ->send(new ProductMailing1($seccion, $this->cliente));

        if ($lead) {
            EmailMessage::create([
                'lead_id' => $lead->id,
                'type' => 'popup',
                'subject' => $seccion->titulo,
                'body' => $seccion->parrafo1,
                'status' => 'enviado',
                'sent_at' => now(),
            ]);
        }

        Log::info('Email de producto enviado', [
            // 'correo' => $this->cliente['correo'],
            'corre' => $this->cliente['email'],
            'paso' => $this->paso,
        ]);

    } catch (\Exception $e) {
            Log::error('Error enviando email de producto', [
                'correo' => $this->cliente['correo'],
                'paso' => $this->paso,
                'error' => $e->getMessage(),
            ]);

            if ($lead) {
                EmailMessage::create([
                    'lead_id' => $lead->id,
                    'type' => 'popup',
                    'subject' => $seccion->titulo,
                    'body' => $seccion->parrafo1,
                    'status' => 'fallido',
                    'sent_at' => now(),
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
}
}