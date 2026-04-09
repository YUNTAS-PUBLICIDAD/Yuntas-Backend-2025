<?php
namespace App\Application\Services\Product;

class ProductDetector
{
  // protected array $keywords = [
  // 'producto', 'productos', 'led', 'neon', 'letrero', 'letreros'
  // ];

  protected array $listKeywords = [
    'productos', 'catalogo', 'catálogo', 'que tienes', 'mostrar productos'
  ];

  protected array $searchKeywords = [
    'producto', 'led', 'neon', 'neón', 'letrero', 'letreros'
  ];

  // public function match (string $message): bool
  // {
  //   $message = strtolower($message);

  //   foreach($this->keywords as $word){
  //     if(str_contains($message, $word)){
  //       return true;
  //     }
  //   }
  //   return false;
  // }

  public function detectType(string $message): string
  {
    $message = strtolower($message);

    // Exploración (listado)
    foreach ($this->listKeywords as $word){
      if(str_contains($message, $word)){
        return 'list';
      }
    }
    // Búsqueda espeçífica
    foreach($this->searchKeywords as $word){
      if(str_contains($message, $word)){
        return 'search';
      }
    }
    return 'none';
  }
}
