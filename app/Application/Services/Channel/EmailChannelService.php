<?php
namespace App\Application\Services\Channel;

use App\Mail\AutomationTemplateMail;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailChannelService
{
    public function send(array $payload): array
    {
        $this->validate($payload);

        try {

            Mail::to(
                $payload['to']
            )->send(

                new AutomationTemplateMail(
                    $payload
                )
            );

            Log::info('Email enviado', [
                'to' => $payload['to'],
            ]);

            return [

                'success' => true,

                'provider' => 'email',
            ];

        } catch (\Throwable $e) {

            Log::error('Error enviando email', [

                'message' => $e->getMessage(),

                'payload' => $payload,
            ]);

            throw $e;
        }
    }

    // =====================================================
    // VALIDATION
    // =====================================================

    protected function validate(
        array $payload
    ): void {

        if (empty($payload['to'])) {

            throw new Exception(
                'Email recipient requerido'
            );
        }

        if (empty($payload['subject'])) {

            throw new Exception(
                'Email subject requerido'
            );
        }

        if (
            empty($payload['content']) &&
            empty($payload['image_url'])
        ) {

            throw new Exception(
                'Email vacío'
            );
        }
    }
}
