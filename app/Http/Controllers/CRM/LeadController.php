<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\Services\CRM\LeadService;
use App\Application\DTOs\CRM\LeadDTO;
use App\Http\Requests\CRM\StoreLeadRequest; 
use App\Http\Requests\CRM\UpdateLeadRequest; 

class LeadController extends Controller
{
    public function __construct(
        private LeadService $leadService
    ) {}

    /**
     * Registrar un nuevo interesado (Desde formulario Web)
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        try {
            $dto = LeadDTO::fromRequest($request);
            $lead = $this->leadService->create($dto);

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
     * Listar leads
     */
    public function index(Request $request): JsonResponse
    {
        $leads = $this->leadService->getAll($request->get('perPage', 20));
        return response()->json(['success' => true, 'data' => $leads]);
    }

     public function update(UpdateLeadRequest $request, $id): JsonResponse
    {
        try {
            $dto = LeadDTO::fromRequest($request);
            $lead = $this->leadService->update($id, $dto);
            return response()->json([
                'success' => true,
                'message' => 'Lead actualizado',
                'data' => $lead
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->leadService->delete($id);
            return response()->json(['success' => true, 'message' => 'Lead eliminado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}