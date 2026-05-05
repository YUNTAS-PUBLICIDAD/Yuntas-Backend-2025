<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Service\Image\ImageService;
use Illuminate\Http\Request;

class TemplateAssetController extends Controller
{
  public function __construct(private ImageService $imageService)
  {}

  public function store(Request $request)
  {
    $request->validate([
      'file' => 'required|file|mimes:webp|max:2048'
    ]);

    $path = $this->imageService->store(
      $request->file('file'),
      'templates'
    );

    return response()->json(
      [
        'path' => $path
      ]
    );
  }
}
