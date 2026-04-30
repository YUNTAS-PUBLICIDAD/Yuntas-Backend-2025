<?php
namespace App\Application\Services\Chatbot\Context;

class ChatContext
{
  public ?string $name = null;
  public ?string $phone = null;
  public ?string $state = 'ready';
  public array $data = [];

  public static function fromArray(?array $data): self
  {
    $ctx = new self();
    // if(!$data) return $ctx;
    if(!$data){
      $ctx->data = [];
      return $ctx;
    }

    $ctx->name = data_get($data, 'user.name');
    $ctx->phone = data_get($data, 'user.phone');
    $ctx->state = data_get($data, 'conversation.state', 'ready');

    $ctx->data = data_get($data, 'data', []);

    return $ctx;
  }

  public function toArray(): array
  {
    return [
    'user' => [
      'name' => $this->name,
      'phone' => $this->phone,
    ],
    'conversation' => [
      'state' => $this->state
    ],
    'data' => $this->data
    ];
  }
}
