<?php

namespace App\Http\Controllers\Template;

use App\Application\Services\Template\TemplateResolverService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TemplateResolverController extends Controller
{
  public function __construct(private TemplateResolverService $resolver)
  {
  }

  // Simula resolución de template por evento
  public function resolve(Request $request)
  {
    $data = $request->validate([
      'event' => 'required|string',
      'context' => 'array'
    ]);

    $result = $this->resolver->resolve(
      $data['event'],
      $data['context'] ?? []
    );

    if(!$result){
      return response()->json([
        'message' => 'No template matched for this event',
        'data' => null
      ], 404);
    }

    return response()->json([
      'message' => 'Template resolved successfully',
      'data' => $result
    ]);
  }
}
