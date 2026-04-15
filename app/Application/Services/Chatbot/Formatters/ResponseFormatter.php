<?php

namespace App\Application\Services\Chatbot\Formatters;

class ResponseFormatter
{
  public function format($text, $metadata, $channel)
  {
   return match($channel) {
     'web' => $this->formatWeb($text, $metadata),
     'whatsapp' => $this->formatWhatsapp($text, $metadata),
     default => $this->formatWeb($text, $metadata)
   };
  }

  protected function formatWeb($text, $metadata)
  {
   return [
    'text' => $text,
    'metadata' => $metadata
   ];
  }

  protected function formatWhatsapp($text, $metadata)
  {
    $extra = '';

    //     if ($metadata) {
    //         foreach ($metadata as $item){
    //             if(($item['type'] ?? null) === 'products'){
    //                 $products = $item['products'] ?? [];

    //                 foreach ($products as $p){
    //                     $extra .= "\n• {$p['name']} - S/ {$p['price']}";
    //                     $extra .= "\nhttps://tusitio.com/productos/{$p['slug']}\n";
    //                 }
    //             }

    //             if(($item['type'] ?? null) === 'whatsapp'){
    //                 $extra .= "\nHabla con un asesor:\nhttps://wa.me/XXXXXXXXX";
    //             }

    //             if(($item['type'] ?? null) === 'contact_page'){
    //                 $extra .= "\nMás info:\nhttps://tusitio.com{$item['url']}";
    //             }
    //         }
    //     }
    //

    if ($metadata && ($metadata['type'] ?? null) === 'products') {
           foreach ($metadata['products'] ?? [] as $p) {
               $extra .= "\n• {$p['name']} - S/ {$p['price']}";
               $extra .= "\nhttps://tusitio.com/productos/{$p['slug']}\n";
           }
       }

       if ($metadata && ($metadata['type'] ?? null) === 'contact') {
           $extra .= "\nHabla con un asesor:\nhttps://wa.me/XXXXXXXXX";
       }

       return [
           'text' => trim($text . "\n" . $extra),
           'metadata' => $metadata
       ];

        return [
            'text' => trim($text . "\n" . $extra),
            'metadata' => $metadata
        ];
  }
}
