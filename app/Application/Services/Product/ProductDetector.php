<?php
namespace App\Application\Services\Product;

class ProductDetector
{

  protected array $listKeywords = [
    'productos', 'catalogo', 'catálogo', 'que tienes', 'mostrar productos', 'mostrar catálogo', 'servicios'
  ];


  public function detectType(string $message): string
  {
    $message = strtolower($message);

    // Exploración (listado)
    foreach ($this->listKeywords as $word){
      if(str_contains($message, $word)){
        return 'list';
      }
    }
    return 'none';
  }
}
