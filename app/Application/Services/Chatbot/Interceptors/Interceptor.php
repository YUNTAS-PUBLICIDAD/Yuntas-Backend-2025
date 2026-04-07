<?php

namespace App\Application\Services\Chatbot\Interceptors;

use App\Application\Services\Chatbot\Context\ChatContext;

interface Interceptor {
  public function handle($conversation, $message, ChatContext $context): InterceptionResult;
}
