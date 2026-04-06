<?php

namespace App\Application\Services\Chatbot\Interceptors;

class InterceptionResult {
  public bool $stop = false;
  public ?string $response = null;
  public string $message;

  public static function continue($message){
    $r = new self;
    $r->message = $message;
    return $r;
  }

  public static function stop($response){
    $r = new self;
    $r->stop = true;
    $r->response = $response;
    return $r;
  }
}
