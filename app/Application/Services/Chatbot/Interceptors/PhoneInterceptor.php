<?php
namespace App\Application\Services\Chatbot\Interceptors;

use App\Application\Services\Chatbot\Context\ChatContext;
use App\Application\Services\Chatbot\MessageParser;
use App\Application\Services\Lead\LeadNotifier;

class PhoneInterceptor implements Interceptor
{
  public function handle($conversation, $message, ChatContext $context): InterceptionResult
  {
    $phone = app(MessageParser::class)->extractPhone($message);

    if(!$phone){
      return InterceptionResult::continue($message);
    }
    if($context->phone){
      return InterceptionResult::stop('Ya tengo tu número 👍');
    }
    $context->phone = $phone;
    app(LeadNotifier::class)->notify($conversation);

    return InterceptionResult::stop('Te contactamos en breve 👍');
  }
}
