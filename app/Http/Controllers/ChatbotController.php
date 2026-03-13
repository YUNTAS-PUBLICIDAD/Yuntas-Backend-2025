<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\ChatbotConversation;
use App\Models\Product;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class ChatbotController extends Controller
{

  public function message(Request $request)
  {

    try {
      // Validación
      $request->validate([
        'message' => 'required|string|max:500',
        'session_id' => 'required|string'
      ]);

      $sessionId = $request->session_id;
      $message = strtolower($request->message);
      // $message = $this->normalizeText($request->message);
      // $normalized = $this->normalizeText($request->message);
      // $message = $request->message;

      // GUARDAR MENSAJE USUARIO
      // ChatbotConversation::create([
      //   'session_id' => $sessionId,
      //   'role' => 'user',
      //   'message' => $message
      // ]);
      ChatbotConversation::create([
        'session_id' => $sessionId,
        'data' => json_encode([
          'role' => 'user',
          'text' => $message
          // 'text' => $normalized
        ])
      ]);

      // Obtener respuesta del bot según patrones
      // $bot = $this->getBotReply($normalized);
      $bot = $this->getBotReply($message);

      /*
         |--------------------------------------------------------------------------
         | SALUDO
         |--------------------------------------------------------------------------
         */
      // if ($this->contains($message, ['hola', 'buenas', 'hello'])) {
      //   $reply = 'Hola 👋 ¿En qué puedo ayudarte?';
      //   return $this->botResponse($sessionId, 'text', $reply);
      // }
      /*
             |--------------------------------------------------------------------------
             | CONTACTO
             |--------------------------------------------------------------------------
             */

      // if ($this->contains($message, ['soporte', 'ayuda', 'contacto'])) {
      //   $reply = 'Puedes enviarnos un mensaje aquí';

      //   return $this->botResponse($sessionId, 'contact', $reply, [
      //     'url' => '/contacto'
      //   ]);
      // }

      /*
         |--------------------------------------------------------------------------
         | BUSCAR PRODUCTOS
         |--------------------------------------------------------------------------
         */

      // $products = Product::with(['images' => function ($q) {
      //   $q->limit(1);
      // }])->where(function ($query) use ($message) {
      //   $query->where('name', 'like', '%'.$message.'%')
      //     ->orWhere('description', 'like', '%'.$message.'%');
      // })
      //   ->limit(3)
      //   ->get();

      // if ($products->count()) {
      //   $reply = 'Encontre estos productos:';

      //   return $this->botResponse($sessionId, 'products', $reply, [
      //     'products' => $products
      //   ]);
      // }

      // Si el tipo es products y extraProducts está activo
      if ($bot['type'] === 'products' && $bot['extraProducts']) {
        $products = Product::with(['images' => function ($q) {
          $q->limit(1);
        }])->where(function ($query) use ($message) {
          $query->where('name', 'like', '%'.$message.'%')
            ->orWhere('description', 'like', '%'.$message.'%');
        })->limit(3)->get();

        if ($products->count()) {
          return $this->botResponse($sessionId, 'products', $bot['reply'], [
            'products' => $products
          ]);
        } else {
          return $this->botResponse($sessionId, 'text', 'No encontramos productos que coincidan. Puedes contactarnos para más información.');
        }
      }

      // flujo de productos + contacto
      if ($bot['type'] === 'product_contact') {
        // Buscar productos
        $products = Product::with(['images' => function ($q) {
          $q->limit(1);
        }])
          // ->where(function ($q) use ($message) {
          //   $q->whereRaw("name LIKE ? COLLATE utf8_general_ci", ["%{$message}%"])
          //     ->orWhereRaw("description LIKE ? COLLATE utf8_general_ci", ["%{$message}%"]);
          // })
          // ->whereRaw("LOWER(name) LIKE ?", ["%{$search}%"])
          // ->orWhereRaw("LOWER(description) LIKE ?", ["%{$search}%"])
          // ->where(function ($q) use ($normalized) {
          //   $q->where('name', 'like', '%'.$normalized.'%')
          //     ->orWhere('description', 'like', '%'.$normalized.'%');
          // })
          ->limit(3)
          ->get();

        if ($products->count()) {
          // Respuesta combinada productos + botón WhatsApp
          return $this->botResponse($sessionId, 'product_contact', $bot['reply'], [
            'products' => $products ?? [],
            'whatsapp_url' => $bot['extra']['whatsapp_url'] ?? null,
          ]);
        }
        // else {
        //   // Si no hay productos, ir directo a contacto
        //   return $this->botResponse($sessionId, 'contact',
        //     'No encontramos productos exactos 😅. Para el precio exacto, escríbenos por WhatsApp 📲',
        //     ['url' => $bot['extra']['whatsapp_url'] ?? '/contacto']
        //   );
        // }
      }

      /*
             |--------------------------------------------------------------------------
             | BUSCAR BLOG
             |--------------------------------------------------------------------------
             */
      // $blogs = Blog::where('title', 'like', "%$message%")
      //   ->limit(3)
      //   ->get();

      // if ($blogs->count()) {
      //   $reply = 'Estos artículos pueden ayudarte:';

      //   return $this->botResponse($sessionId, 'blogs', $reply, [
      //     'blogs' => $blogs
      //   ]);
      // }

      // Información de la empresa
      // if ($this->contains($message, ['qué hacen', 'a qué se dedican', 'empresa', 'servicios'])) {
      //   $reply = 'Somos especialistas en letreros luminosos y soluciones LED para negocios, eventos y espacios públicos. ✨';
      //   return $this->botResponse($sessionId, 'text', $reply);
      // }

      // Productos LED

      // if ($this->contains($message, ['monitores', 'pantallas', 'led', 'digital'])) {
      //   $products = Product::with(['images' => function ($q) {
      //     $q->limit(1);
      //   }])->where('category', 'LED')->limit(3)->get();

      //   if ($products->count()) {
      //     $reply = 'Estos son nuestros productos LED destacados:';
      //     return $this->botResponse($sessionId, 'products', $reply, [
      //       'products' => $products
      //     ]);
      //   } else {
      //     $reply = 'Actualmente no tenemos productos LED destacados. Puedes contactarnos para más información.';
      //     return $this->botResponse($sessionId, 'text', $reply);
      //   }
      // }

      // Cotizaciones
      // if ($this->contains($message, ['cotización', 'precio', 'contacto'])) {
      //   $reply = 'Puedes enviarnos un mensaje para cotización y uno de nuestros asesores te responderá.';
      //   return $this->botResponse($sessionId, 'contact', $reply, [
      //     'url' => '/contacto'
      //   ]);
      // }

      // Ubicación
      // if ($this->contains($message, ['dónde', 'ubicación', 'dirección', 'horario', 'atención'])) {
      //   $reply = 'Estamos en Lima, Perú. Nuestro horario es Lunes a Viernes de 9:00 a 18:00. 📍';
      //   return $this->botResponse($sessionId, 'text', $reply);
      // }

      /*
          |--------------------------------------------------------------------------
          | FALLBACK
          |--------------------------------------------------------------------------
          */

      // $reply = 'No encontré algo exacto 🤔 ¿Buscas algún producto específico?';

      // $reply = 'No encontré algo exacto 🤔 ¿Buscas algún producto LED o quieres hablar con un asesor?';
      // return $this->botResponse($sessionId, 'fallback', $reply);
      return $this->botResponse($sessionId, $bot['type'], $bot['reply'], $bot['extra'] ?? []);
    } catch (\Throwable $e) {
      // Loguea el error para debug
      \Log::error('Error en ChatbotController@message: '.$e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'request' => $request->all()
      ]);

      // Respuesta genérica al frontend
      return response()->json([
        'role' => 'bot',
        'text' => 'Ups! Algo salió mal 😅. Intenta de nuevo.',
        'type' => 'error'
      ], 500);
    }


  }
  /*
     |--------------------------------------------------------------------------
     | HISTORIAL
     |--------------------------------------------------------------------------
     */
  public function history($sessionId)
  {

  try {
    
    $messages = ChatbotConversation::where('session_id', $sessionId)->latest()->limit(50)->get()->reverse()->values();
    // $messages = ChatbotConversation::where('session_id', $sessionId)
    //   ->latest()
    //   ->skip(50)
    //   ->take(PHP_INT_MAX)
    //   ->delete();

    // return response()->json($messages);
    // return response()->json(
    //   $messages->map(function ($m) {
    //     $payload = $m->payload ? json_decode($m->payload, true) : [];

    //     return [
    //       'role' => $m->role,
    //       'text' => $m->message,
    //       ...$payload
    //     ];
    //   })
    // );
    return response()->json(
      // $messages->map(fn ($m) => json_decode($m->data, true))
      $messages->map(fn ($m) => $m->data)
    );
  } catch (\Throwable $th) {
  }
  }

  /*
    |--------------------------------------------------------------------------
    | RESPUESTA BOT (helper)
    |--------------------------------------------------------------------------
    */
  private function botResponse($sessionId, $type, $reply, $extra = [])
  {

    $data = [
      'role' => 'bot',
      'text' => $reply,
      'type' => $type,
      ...$extra
    ];
    ChatbotConversation::create([
      'session_id' => $sessionId,
      'data' => json_encode($data),
    ]);

    // LIMPIAR HISTORIAL (solo mantener últimos 50)
    $this->cleanOldMessages($sessionId);

    return response()->json($data);
  }

  private function cleanOldMessages($sessionId)
  {
    $idsToDelete = ChatbotConversation::where('session_id', $sessionId)->orderBy('id', 'desc')
      ->skip(50)
      ->pluck('id');

    if ($idsToDelete->count()) {
      ChatbotConversation::whereIn('id', $idsToDelete)->delete();
    }
  }

  /*
    |--------------------------------------------------------------------------
    | CONTAINS HELPER
    |--------------------------------------------------------------------------
    */

  private function contains($text, array $words): bool
  {
    foreach ($words as $word) {
      if (str_contains($text, $word)) {
        return true;
      }
    }
    return false;
  }

  private function normalizeText(string $text): string
  {
    $text = strtolower($text);
    $text = str_replace(
      ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'],
      ['a', 'e', 'i', 'o', 'u', 'u', 'n'],
      $text
    );
    return $text;
  }

  // Lista de patrones y respuestas
  private function getBotReply(string $message): array
  {
    $patterns = [
      ['keywords' => ['hola', 'buenas', 'hello'],
        'type' => 'text',
        'reply' => 'Hola 👋 ¿En qué puedo ayudarte?'],

      ['keywords' => ['soporte', 'ayuda', 'contacto'],
        'type' => 'contact',
        'reply' => 'Puedes enviarnos un mensaje aquí',
        'extra' => ['url' => '/contacto']],

      // ['keywords' => ['precio', 'cotización', 'cotizar'],
      //   'type' => 'contact',
      //   'reply' => 'Puedes enviarnos un mensaje para cotización y uno de nuestros asesores te responderá.',
      //   'extra' => ['url' => '/contacto']],

      ['keywords' => ['precio', 'cuanto cuesta', 'cotizar', 'cotización', 'valor', 'costo'],
        'type' => 'product_contact', // tipo especial para nuestro flujo
        'reply' => 'Te muestro algunos productos que podrían interesarte 👇',
        'extra' => [
          'whatsapp_url' => 'https://wa.me/51987654321?text='.urlencode('Hola! Quiero cotizar este producto.')
        ]

      ],

      ['keywords' => ['ubicacion', 'dirección', 'horario', 'atención'],
        'type' => 'text',
        'reply' => 'Estamos en Lima, Perú. Nuestro horario es Lunes a Viernes de 9:00 a 18:00. 📍'],

      ['keywords' => ['empresa', 'servicios', 'que hacen', 'a que se dedican'],
        'type' => 'text',
        'reply' => 'Somos especialistas en letreros luminosos y soluciones LED para negocios, eventos y espacios públicos. ✨'],

      // ['keywords' => ['monitores', 'pantallas', 'led', 'digital'],
      //   'type' => 'products',
      //   'reply' => 'Estos son nuestros productos LED destacados:',
      //   'extraProducts' => true], // indica que hay que buscar en DB
    ];

    foreach ($patterns as $pattern) {
      foreach ($pattern['keywords'] as $word) {
        if (str_contains($message, $word)) {
          return [
            'type' => $pattern['type'],
            'reply' => $pattern['reply'],
            'extra' => $pattern['extra'] ?? [],
            'extraProducts' => $pattern['extraProducts'] ?? false
          ];
        }
      }
    }

    // fallback
    return [
      'type' => 'fallback',
      'reply' => 'Puedo ayudarte con productos LED, precios o cotizaciones. ¿Qué estás buscando?'
    ];
  }
}
