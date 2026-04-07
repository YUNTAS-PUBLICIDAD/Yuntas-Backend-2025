<?php

namespace App\Application\Services\Chatbot\States;

use App\Application\Services\Chatbot\Context\ChatContext;

interface State {
  public function handle($conversation, $message, ChatContext $context): ?string;
}
