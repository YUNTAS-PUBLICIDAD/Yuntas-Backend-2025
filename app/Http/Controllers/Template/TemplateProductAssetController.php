<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Models\ProductTemplateAsset;
use App\Service\Image\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TemplateProductAssetController extends Controller
{
  public function __construct(private ImageService $imageService)
  {
  }

  public function upload(Request $request)
  {

    Log::info('UPLOAD DEBUG', [
      'has_file' => $request->hasFile('file'),
      'file' => $request->file('file'),
      'all' => $request->all()
    ]);

    $data = $request->validate([
      'file' => 'required|file|mimes:webp|max:2048'
    ]);

    $path = $this->imageService->store(
      $data['file'],
      'templates/products'
    );

    return response()->json([
      'path' => $path
    ]);
  }

  public function destroy(Request $request)
  {
    $data = $request->validate([
    'product_id' => 'required|integer',
    'variant_id' => 'required|integer',
    'key' => 'required|string',
    ]);

 $asset =  ProductTemplateAsset::where([
    'product_id' => $data['product_id'],
    'template_variant_id' => $data['variant_id'],
    'key' => $data['key'],
    ])->first();

    if(!$asset){
      return response()->json(['message' => 'Not found'], 404);
    }
    // Eliminar archivo fisico
   $deleted = $this->imageService->remove($asset->path);

   Log::info('DELETE PRODUCT TEMPLATE ASSET', [
    'path' => $asset->path,
    'file_deleted' => $deleted
   ]);

    // Eliminar registro DB
    $asset->delete();

    return response()->json([
      'message' => 'deleted'
    ]);
  }
}
