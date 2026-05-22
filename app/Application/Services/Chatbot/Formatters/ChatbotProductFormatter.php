<?php

namespace App\Application\Services\Chatbot\Formatters;

use App\Models\Product;

class ChatbotProductFormatter
{
  public function transform(Product $product): array
  {
    $image = $product->images->firstWhere('slot.name', 'List');

    return [
      'id' => $product->id,
      'name' => $product->name,
      'slug' => $product->slug,
      'price' => $product->price,
      'image' => $image?->url
    ];
  }
}
