<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; 
use App\Application\Services\Support\ClaimService;
use App\Application\DTOs\Support\CreateClaimDTO;
use App\Application\DTOs\Support\ReplyClaimDTO;

use App\Http\Requests\Support\StoreClaimRequest;
use App\Http\Requests\Support\ReplyClaimRequest;

class ClaimController extends Controller
{
    public function __construct(
        private ClaimService $service
    ) {}

    public function store(StoreClaimRequest $request): JsonResponse
    {
        try {

            $dto = CreateClaimDTO::fromRequest($request);
            $claim = $this->service->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Reclamo registrado. Se le envió una copia a su correo.',
                'data' => $claim
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Admin: Listar Reclamos
    public function index(Request $request): JsonResponse
    {
        $claims = $this->service->getAll($request->get('perPage', 20));
        return response()->json(['success' => true, 'data' => $claims]);
    }

    // Admin: Ver Detalle
    public function show($id): JsonResponse
    {
        try {
            $claim = $this->service->getDetail($id);
            return response()->json(['success' => true, 'data' => $claim]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // Admin: Responder Reclamo (Usamos ReplyClaimRequest)
    public function reply(ReplyClaimRequest $request, $id): JsonResponse
    {
        try {
            $dto = new ReplyClaimDTO(
                admin_id: $request->user()->id,
                message: $request->input('message'),
                send_email: $request->boolean('send_email', true)
            );

            $response = $this->service->reply($id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Respuesta guardada correctamente',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}