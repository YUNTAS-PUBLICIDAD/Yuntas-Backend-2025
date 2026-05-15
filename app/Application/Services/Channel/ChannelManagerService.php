<?php

namespace App\Application\Services\Channel;

use Exception;

// Decide que canal usar
class ChannelManagerService
{
    public function send(
        string $channel,
        array $payload
    ) {

        return match ($channel) {

            'whatsapp'
                => app(
                    WhatsAppChannelService::class
                )->send($payload),

            'email'
                => app(
                    EmailChannelService::class
                )->send($payload),

            default
                => throw new Exception(
                    'Canal inválido'
                ),
        };
    }
}
