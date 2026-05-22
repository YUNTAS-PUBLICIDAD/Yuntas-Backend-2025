<?php

namespace App\Application\Services\Product;

use App\Application\Services\Chatbot\Formatters\ChatbotProductFormatter;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductSearchService
{
  protected array $importantShortWords = [
         'led',
         'rgb',
         'lcd',
         'neo',
         'p4',
         'p5'
     ];

     public function search(
         string $query,
         int $limit = 3
     ): Collection {

         $query = $this->normalize($query);

         $tokens = $this->tokenize($query);

         if(empty($tokens)){
             return collect();
         }

         return Product::query()
            ->with('images.slot')
            ->select(
              'id',
              'name',
              'slug',
              'price'
            )
             ->get()

             ->map(function ($product) use ($query, $tokens) {

                 $score = $this->scoreProduct(
                     $product,
                     $query,
                     $tokens
                 );

                 return [
                     'score' => $score,
                     'product' => $product
                 ];
             })

             ->filter(
                 fn ($item) => $item['score'] > 0
             )

             ->sortByDesc('score')

             ->take($limit)

             ->values();

     }

     protected function scoreProduct(
     Product $product,
     string $query,
     array $tokens
     ): int {
       $score = 0;
       $name = $this->normalize($product->name);

       // Exact full match
       if($name === $query){
         $score += 100;
       }

       // Contains full phrase
       if(str_contains($name, $query)){
         $score += 50;
       }

       foreach($tokens as $token){
         // Exact token
         if($token === $name){
           $score += 80;
           continue;
         }

         // Starts with
         if(str_starts_with($name, $token)){
           $score += 25;
         }

         // Contains token
         if(str_contains($name, $token)){
           $score += 15;
         }
       }
       return $score;
     }

     protected function tokenize(string $text): array {
       return collect(explode(' ', $text))->map(fn ($w) => trim($w))->filter(function ($word) {
         if(in_array($word, $this->importantShortWords)){
           return true;
         }
         return strlen($word) > 3;
       })->values()
       ->toArray();
     }

     protected function normalize(string $text): string {
       $text = mb_strtolower($text);

       return Str::ascii($text);
     }
}
