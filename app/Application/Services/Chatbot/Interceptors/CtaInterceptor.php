<?php
namespace App\Application\Services\Chatbot\Interceptors;

use App\Application\Services\Chatbot\Context\ChatContext;
use App\Application\Services\Chatbot\Interceptors\Interceptor;

class CtaInterceptor implements Interceptor
{
  public function handle($conversation, $message, ChatContext $context):InterceptionResult
  {
    $msg = strtolower(trim($message));

    // Whatsapp / asesor
    if (preg_match('/asesor|whatsapp|hablar|agente/', $msg)) {
                return InterceptionResult::stopWithMetadata(
                    'Te paso con un asesor ahora 👇',
                    [
                        'type' => 'whatsapp'
                    ]
                );
            }
    // Página contacto / nosotros
    if (preg_match('/contacto|ubicacion|direccion|oficina/', $msg)) {
                return InterceptionResult::stopWithMetadata(
                    'Aquí tienes nuestra información 👇',
                    [
                        'type' => 'contact_page',
                        'url' => '/contacto'
                    ]
                );
            }
            return InterceptionResult::continue($message);
  }
}
