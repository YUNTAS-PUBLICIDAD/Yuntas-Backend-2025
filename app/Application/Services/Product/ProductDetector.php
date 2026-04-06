<?php
namespace App\Application\Services\Product;

class ProductDetector
{
  protected array $keywords = [
  'producto', 'productos', 'led', 'neon', 'letrero', 'letreros'
  ];

  public function match (string $message): bool
  {
    $message = strtolower($message);

    foreach($this->keywords as $word){
      if(str_contains($message, $word)){
        return true;
      }
    }
    return false;
  }
}
