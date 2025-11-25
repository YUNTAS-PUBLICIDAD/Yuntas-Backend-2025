<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\Services\Blog\BlogService;
use App\Application\DTOs\Blog\BlogDTO;
use App\Http\Requests\PostBlog\PostStoreBlog; 
use App\Http\Resources\Blog\BlogResource;

class BlogController extends Controller
{
    public function __construct(
        private BlogService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $blogs = $this->service->getAll($request->get('perPage', 10));
        return response()->json([
            'success' => true,
            'data' => BlogResource::collection($blogs)->response()->getData(true)
        ]);
    }

    public function show($slug): JsonResponse
    {
        try {
            $blog = $this->service->getDetail($slug);
            return response()->json(['success' => true, 'data' => new BlogResource($blog)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function store(Request $request): JsonResponse 
    {
        try {
            $dto = BlogDTO::fromRequest($request); 
            $blog = $this->service->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Blog publicado exitosamente',
                'data' => new BlogResource($blog)
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $id): JsonResponse
{
    try {
        $dto = BlogDTO::fromRequest($request);
        $blog = $this->service->update($id, $dto);

        return response()->json([
            'success' => true,
            'message' => 'Blog actualizado correctamente',
            'data' => new BlogResource($blog)
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    
    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return response()->json(['success' => true, 'message' => 'Blog eliminado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}