<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

// Servicios
use App\Application\Services\Product\CreateProductService;
use App\Application\Services\Product\GetProductCatalogService; 
use App\Application\Services\Product\GetProductDetailService;  
use App\Application\Services\Product\UpdateProductService;
use App\Application\Services\Product\DeleteProductService;


// DTOs y Resources
use App\Application\DTOs\Product\ProductDTO;
use App\Http\Requests\Producto\StoreProductoRequest;
// use App\Http\Requests\Producto\UpdateProductoRequest; // (Si tienes uno, úsalo)
use App\Http\Resources\Product\ProductResource;
class ProductController extends Controller
{
    public function __construct(
        private CreateProductService $createService,
        private GetProductCatalogService $getCatalogService, 
        private GetProductDetailService $getDetailService, 
        private UpdateProductService $updateService,
        private DeleteProductService $deleteService   
    ) {}

    /**
     * Listar productos (Paginado)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        
        $products = $this->getCatalogService->execute((int)$perPage);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products)->response()->getData(true)
        ]);
    }

    /**
     * Ver detalle de un producto por Slug (o ID si prefieres adaptar el servicio)
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $product = $this->getDetailService->execute($slug);

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
        public function update(Request $request, $id): JsonResponse
        {
            try {
                $dto = ProductDTO::fromRequest($request);
                
                $product = $this->updateService->execute($id, $dto);

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
                $this->deleteService->execute($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado correctamente'
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

}