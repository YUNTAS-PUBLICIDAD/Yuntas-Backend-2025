<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\Services\Support\ContactService;
use App\Application\DTOs\Support\CreateContactDTO;

class ContactMessageController extends Controller
{
    public function __construct(
        private ContactService $service
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate(['first_name' => 'required', 'message' => 'required']);

        try {
            $dto = CreateContactDTO::fromRequest($request);
            $contact = $this->service->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado.',
                'data' => $contact
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $messages = $this->service->getAll($request->get('perPage', 20));
        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function show($id): JsonResponse
    {
        try {
            $contact = $this->service->getById($id);
            return response()->json(['success' => true, 'data' => $contact]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return response()->json(['success' => true, 'message' => 'Mensaje eliminado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}