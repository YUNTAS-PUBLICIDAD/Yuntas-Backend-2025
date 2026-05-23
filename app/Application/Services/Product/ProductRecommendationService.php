<?php

namespace App\Application\Services\Product;

use App\Application\Services\Chatbot\Formatters\ChatbotProductFormatter;
use App\Models\Product;

class ProductRecommendationService
{
  public function recommend(array $entities)
  {
    return Product::query()
    ->when(
      !empty($entities['product_id']),
      fn($q) => $q->where('id', $entities['product_id'])
    )
    ->take(6)
    ->get()
    ->map(fn ($product) => app(ChatbotProductFormatter::class)->transform($product));
  }
}
