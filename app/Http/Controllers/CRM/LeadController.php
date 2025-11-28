<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\Services\CRM\LeadService;
use App\Application\DTOs\CRM\LeadDTO;
use App\Http\Requests\CRM\StoreLeadRequest; 

class LeadController extends Controller
{
    public function __construct(
        private LeadService $service
    ) {}

    /**
     * Registrar un nuevo interesado (Desde formulario Web)
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        try {
            $dto = LeadDTO::fromRequest($request);
            $lead = $this->service->create($dto);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud recibida correctamente. Nos pondremos en contacto pronto.',
                'data' => $lead
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar leads (Solo para Admin)
     */
    public function index(Request $request): JsonResponse
    {
        $leads = $this->service->getAll($request->get('perPage', 20));
        return response()->json(['success' => true, 'data' => $leads]);
    }
}