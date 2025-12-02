<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Application\Services\Product\ProductService;
use App\Application\DTOs\Product\ProductDTO;
use App\Http\Resources\Product\ProductResource;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;


/**
 * @OA\Info(
 *     title="Documentación API - Yuntas Backend 2025",
 *     version="1.0.0",
 *     description="API oficial del sistema Yuntas",
 *     @OA\Contact(email="your-email@gmail.com")
 * )
 *
 * @OA\Tag(
 *     name="Productos",
 *     description="Endpoints para gestión de productos"
 * )
 */
class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/productos",
     *     summary="Listar productos",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Cantidad por página",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos obtenida"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getAll($request->get('per_page', 10));
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products)->response()->getData(true)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/productos/{slug}",
     *     summary="Obtener detalle de un producto",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug del producto",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Detalle del producto"),
     *     @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function show($slug): JsonResponse
    {
        try {
            $product = $this->productService->getDetail($slug);
            return response()->json(['success' => true, 'data' => new ProductResource($product)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/productos",
     *     summary="Crear un nuevo producto",
     *     tags={"Productos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nombre", "precio", "imagen_principal"},
     *                 @OA\Property(property="nombre", type="string"),
     *                 @OA\Property(property="precio", type="number"),
     *                 @OA\Property(property="imagen_principal", type="file"),
     *                 @OA\Property(property="descripcion", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Producto creado exitosamente"),
     *     @OA\Response(response=500, description="Error al crear producto")
     * )
     */
    public function store(StoreProductRequest $request): JsonResponse

    
    {

        
        try {
            $dto = ProductDTO::fromRequest($request);
            $product = $this->productService->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'data' => new ProductResource($product)
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/productos/{id}",
     *     summary="Actualizar un producto",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="nombre", type="string"),
     *                 @OA\Property(property="precio", type="number"),
     *                 @OA\Property(property="imagen_principal", type="file"),
     *                 @OA\Property(property="descripcion", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Producto actualizado"),
     *     @OA\Response(response=500, description="Error al actualizar producto")
     * )
     */
    public function update(UpdateProductRequest $request, $id): JsonResponse
    {
        try {
            $dto = ProductDTO::fromRequest($request);
            $product = $this->productService->update($id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado',
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/productos/{id}",
     *     summary="Eliminar un producto",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Producto eliminado"),
     *     @OA\Response(response=500, description="Error al eliminar producto")
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->productService->delete($id);
            return response()->json(['success' => true, 'message' => 'Producto eliminado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
