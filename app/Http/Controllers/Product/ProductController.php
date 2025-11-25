<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// 👇 Ahora solo importas UN servicio
use App\Application\Services\Product\ProductService;

use App\Application\DTOs\Product\ProductDTO;
use App\Http\Requests\Producto\StoreProductoRequest;
use App\Http\Resources\Product\ProductResource;

class ProductController extends Controller
{
    // Inyección de dependencia única
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getAll($request->get('per_page', 10));
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products)->response()->getData(true)
        ]);
    }

    public function show($slug): JsonResponse
    {
        try {
            $product = $this->productService->getDetail($slug);
            return response()->json(['success' => true, 'data' => new ProductResource($product)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function store(StoreProductoRequest $request): JsonResponse
    {
        try {
            $dto = ProductDTO::fromRequest($request);
            $product = $this->productService->create($dto); // Llamada al método create

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'data' => new ProductResource($product)
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $dto = ProductDTO::fromRequest($request);
            $product = $this->productService->update($id, $dto); // Llamada al método update

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado',
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->productService->delete($id); // Llamada al método delete
            return response()->json(['success' => true, 'message' => 'Producto eliminado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}