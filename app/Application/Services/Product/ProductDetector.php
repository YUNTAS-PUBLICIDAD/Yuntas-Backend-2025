<?php
namespace App\Application\Services\Product;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

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


  public function detect(string $message): ?Product
  {

    $result = app(ProductSearchService::class)
    ->search($message, 1)
    ->first();

    if(!$result){
    return null;
    }

    if($result['score'] < 15){
    return null;
    }

    return $result['product'];
  }
}
