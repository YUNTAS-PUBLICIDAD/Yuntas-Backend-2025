<?php

namespace App\Application\Services\Lead;

use App\Application\Services\Whatsapp\WhatsappService;
use App\Models\ChatbotConversation;
use Illuminate\Support\Facades\Log;

class LeadNotifier
{
    public function notify(ChatbotConversation $conversation)
    {
        $context = $conversation->context;

       $name = data_get($context, 'user.name');
       $phone = data_get($context, 'user.phone');
       $project = data_get($context, 'lead.project_type');

       if(!$name || !$phone){
         return;
       }

       // $mensaje = "Nuevo lead:\n"
       //                 . "Nombre: {$name}\n"
       //                 . "Proyecto: {$project}";
                       $mensaje = "Hola {$name}, ¿qué tal? 👋\n\n"
                                . "Te escribe un asesor de Yuntas Publicidad.\n"
                                . "Recibimos tu consulta sobre {$project} y ya estamos listos para ayudarte.\n\n"
                                . "¿Tienes alguna idea en mente o quieres que te guiemos desde cero?";

       // Aquí llamas a tu servicio existente
       app(WhatsappService::class)->sendToPhone(
       $phone,
       $mensaje
       );
    }
}
