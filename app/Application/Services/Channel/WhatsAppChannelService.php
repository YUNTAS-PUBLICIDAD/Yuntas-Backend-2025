<?php

namespace App\Application\Services\Channel;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppChannelService
{
    protected string $serviceUrl;

    public function __construct()
    {
        $this->serviceUrl = env(
            'WHATSAPP_SERVICE_URL',
            'http://localhost:3001'
        );
    }

    public function send(array $payload): array
    {
        $this->validate($payload);

        try {

            // =====================================
            // TEXTO
            // =====================================

            if (empty($payload['image_url'])) {

                return $this->sendText(
                    $payload
                );
            }

            // =====================================
            // IMAGEN
            // =====================================

            return $this->sendImage(
                $payload
            );

        } catch (\Throwable $e) {

            Log::error(
                'WhatsApp send failed',
                [
                    'payload' => $payload,
                    'error' => $e->getMessage(),
                ]
            );

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
                'WhatsApp recipient requerido'
            );
        }

        if (
            empty($payload['content']) &&
            empty($payload['image_url'])
        ) {

            throw new Exception(
                'WhatsApp vacío'
            );
        }
    }

    // =====================================================
    // TEXT
    // =====================================================

    protected function sendText(
        array $payload
    ): array {

        $response = Http::timeout(10)

            ->retry(
                3,
                1000,

                function ($exception) {

                    return
                        $exception instanceof ConnectionException;
                }
            )

            ->post(

                "{$this->serviceUrl}/api/whatsapp/send-message",

                [
                    'phone'
                        => $this->normalizePhone(
                            $payload['to']
                        ),

                    'message'
                        => $payload['content'],
                ]
            );

        return $this->handleResponse(
            $response
        );
    }

    // =====================================================
    // IMAGE
    // =====================================================

    protected function sendImage(
        array $payload
    ): array {

        $parsedPath = parse_url(
            $payload['image_url'],
            PHP_URL_PATH
        );

        $imagePath = ltrim(
            str_replace(
                '/storage/',
                '',
                $parsedPath
            ),
            '/'
        );

        $image = Storage::disk('public')
            ->get($imagePath);

        $caption = trim(

            ($payload['content'] ?? '') .

            "\n\n" .

            ($payload['cta_text'] ?? '') .

            ' ' .

            ($payload['cta_url'] ?? '')
        );

        $response = Http::timeout(10)

            ->retry(
                3,
                1000,

                function ($exception) {

                    if (
                        $exception instanceof ConnectionException
                    ) {
                        return true;
                    }

                    if (
                        $exception instanceof RequestException
                    ) {

                        $status =
                            $exception
                                ->response
                                ?->status();

                        return $status >= 500;
                    }

                    return false;
                }
            )

            ->post(

                "{$this->serviceUrl}/api/whatsapp/send-image",

                [

                    'phone'
                        => $this->normalizePhone(
                            $payload['to']
                        ),

                    'imageData'
                        => base64_encode($image),

                    'caption'
                        => $caption,
                ]
            );

        return $this->handleResponse(
            $response
        );
    }

    // =====================================================
    // HELPERS
    // =====================================================

    protected function handleResponse(
        $response
    ): array {

        $data = $response->json();

        if (
            !$response->successful() ||
            !($data['success'] ?? false)
        ) {

            throw new Exception(

                $data['message']
                    ?? 'WhatsApp provider error'
            );
        }

        return [

            'success' => true,

            'provider' => 'whatsapp',

            'chat_id'
                => $data['chatId'] ?? null,

            'response'
                => $data,
        ];
    }

    protected function normalizePhone(
        string $phone
    ): string {

        return strlen($phone) === 9
            ? '51' . $phone
            : $phone;
    }
}
