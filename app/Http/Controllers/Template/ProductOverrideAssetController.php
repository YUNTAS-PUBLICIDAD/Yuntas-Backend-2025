<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Service\Image\ImageService;
use Illuminate\Http\Request;

class ProductOverrideAssetController extends Controller
{
  public function __construct(protected ImageService $imageService) {}

  public function store(Request $request)
  {
    $request->validate([
      'file' => 'required|image|mimes:webp|max:2048',
    ]);

    $path = $this->imageService->store(
    $request->file('file'),
    'templates/overrides'
    );

    return response()->json([
      'key' => 'image',
      'path' => $path,
      'meta' => [
        'url' => asset($path)
      ],
    ]);
  }
}
