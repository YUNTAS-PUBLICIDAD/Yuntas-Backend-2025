<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\Services\CRM\LeadService;
use App\Application\DTOs\CRM\LeadDTO;
use App\Application\Services\Lead\LeadCaptureService;
use App\Http\Requests\CRM\StoreLeadRequest;
use App\Http\Requests\CRM\UpdateLeadRequest;
use App\Models\EmailMessage;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function __construct(
        private LeadService $leadService,
        private LeadCaptureService $leadCaptureService
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

        $leadsWithStats = $leads->through(function ($lead) {
            return array_merge(
                $lead->toArray(),
                ['stats' => $this->getLeadStats($lead->id)]
            );
        });

        return response()->json([
            'success' => true,
            'data' => $leadsWithStats,
            'totals' => $this->calculateGlobalTotals(),
        ]);
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
    // Obtener estadísticas detalladas para un lead específico
    private function getLeadStats($leadId): array
    {
        // Email popup: contar mensajes individuales
        $emailPopupCount = EmailMessage::where('lead_id', $leadId)
            ->where('type', 'popup')
            ->count();

        $lastEmailPopup = EmailMessage::where('lead_id', $leadId)
            ->where('type', 'popup')
            ->latest('sent_at')
            ->first();

        // Email campaign: contar campañas únicas (no mensajes)
        $emailCampaignCount = EmailMessage::where('lead_id', $leadId)
            ->where('type', 'campaign')
            ->whereNotNull('campaign_id')
            ->distinct('campaign_id')
            ->count('campaign_id');

        $lastEmailCampaign = EmailMessage::where('lead_id', $leadId)
            ->where('type', 'campaign')
            ->whereNotNull('campaign_id')
            ->latest('sent_at')
            ->first();

        // WhatsApp popup: contar mensajes individuales
        $whatsappPopupCount = WhatsappMessage::where('lead_id', $leadId)
            ->where('type', 'popup')
            ->count();

        $lastWhatsappPopup = WhatsappMessage::where('lead_id', $leadId)
            ->where('type', 'popup')
            ->latest('sent_at')
            ->first();

        // WhatsApp campaign: contar campañas únicas (no mensajes)
        $whatsappCampaignCount = WhatsappMessage::where('lead_id', $leadId)
            ->where('type', 'campaign')
            ->whereNotNull('campaign_id')
            ->distinct('campaign_id')
            ->count('campaign_id');

        $lastWhatsappCampaign = WhatsappMessage::where('lead_id', $leadId)
            ->where('type', 'campaign')
            ->whereNotNull('campaign_id')
            ->latest('sent_at')
            ->first();

        return [
            'email' => [
                'popup' => [
                    'total_mensajes' => $emailPopupCount,
                    'ultimo_envio' => $lastEmailPopup?->sent_at,
                ],
                'campaign' => [
                    'total_campanas' => $emailCampaignCount,
                    'ultimo_envio' => $lastEmailCampaign?->sent_at,
                ],
            ],
            'whatsapp' => [
                'popup' => [
                    'total_mensajes' => $whatsappPopupCount,
                    'ultimo_envio' => $lastWhatsappPopup?->sent_at,
                ],
                'campaign' => [
                    'total_campanas' => $whatsappCampaignCount,
                    'ultimo_envio' => $lastWhatsappCampaign?->sent_at,
                ],
            ],
        ];
    }

    /**
     * Calcular totales globales de mensajes
     */
    private function calculateGlobalTotals(): array
    {
        // Email campaign: contar campañas únicas
        $emailCampaignCount = DB::table('email_messages')
            ->where('type', 'campaign')
            ->whereNotNull('campaign_id')
            ->distinct('campaign_id')
            ->count('campaign_id');

        // WhatsApp campaign: contar campañas únicas
        $whatsappCampaignCount = DB::table('whatsapp_messages')
            ->where('type', 'campaign')
            ->whereNotNull('campaign_id')
            ->distinct('campaign_id')
            ->count('campaign_id');

        return [
            'email' => [
                'popup' => [
                    'total_mensajes' => EmailMessage::popup()->count(),
                    'exitosos' => EmailMessage::popup()->success()->count(),
                    'fallidos' => EmailMessage::popup()->failed()->count(),
                    'ultimo_envio' => EmailMessage::popup()->latest('sent_at')->first()?->sent_at,
                ],
                'campaign' => [
                    'total_campanas' => $emailCampaignCount,
                    'ultimo_envio' => EmailMessage::campaign()->latest('sent_at')->first()?->sent_at,
                ],
            ],
            'whatsapp' => [
                'popup' => [
                    'total_mensajes' => WhatsappMessage::popup()->count(),
                    'exitosos' => WhatsappMessage::popup()->success()->count(),
                    'fallidos' => WhatsappMessage::popup()->failed()->count(),
                    'ultimo_envio' => WhatsappMessage::popup()->latest('sent_at')->first()?->sent_at,
                ],
                'campaign' => [
                    'total_campanas' => $whatsappCampaignCount,
                    'ultimo_envio' => WhatsappMessage::campaign()->latest('sent_at')->first()?->sent_at,
                ],
            ],
        ];
    }

    public function capture(
    StoreLeadRequest $request
    ): JsonResponse{
      try {
      $lead = $this->leadCaptureService
      ->capture($request->validated());

      return response()->json([
        'success' => true,
        'message' => 'Lead capturado correctamente.',
        'data' => $lead,
      ], 201);
      }catch(\Exception $e){
        return response()->json([
          'success' => false,
          'message' => $e->getMessage()
        ], 500);
      }
    }

}
