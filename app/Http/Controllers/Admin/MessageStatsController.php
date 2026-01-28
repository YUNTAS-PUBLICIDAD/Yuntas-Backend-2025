<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailMessage;
use App\Models\WhatsappMessage;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageStatsController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::with(['emailMessages', 'whatsappMessages'])
            ->get()
            ->map(function ($lead) {
                // Email popup: contar mensajes individuales
                $emailPopupCount = EmailMessage::where('lead_id', $lead->id)
                    ->where('type', 'popup')
                    ->count();

                $lastEmailPopup = EmailMessage::where('lead_id', $lead->id)
                    ->where('type', 'popup')
                    ->latest('sent_at')
                    ->first();

                // Email campaign: contar campañas únicas (no mensajes)
                $emailCampaignCount = EmailMessage::where('lead_id', $lead->id)
                    ->where('type', 'campaign')
                    ->whereNotNull('campaign_id')
                    ->distinct('campaign_id')
                    ->count('campaign_id');

                $lastEmailCampaign = EmailMessage::where('lead_id', $lead->id)
                    ->where('type', 'campaign')
                    ->whereNotNull('campaign_id')
                    ->latest('sent_at')
                    ->first();

                // WhatsApp popup: contar mensajes individuales
                $whatsappPopupCount = WhatsappMessage::where('lead_id', $lead->id)
                    ->where('type', 'popup')
                    ->count();

                $lastWhatsappPopup = WhatsappMessage::where('lead_id', $lead->id)
                    ->where('type', 'popup')
                    ->latest('sent_at')
                    ->first();

                // WhatsApp campaign: contar campañas únicas (no mensajes)
                $whatsappCampaignCount = WhatsappMessage::where('lead_id', $lead->id)
                    ->where('type', 'campaign')
                    ->whereNotNull('campaign_id')
                    ->distinct('campaign_id')
                    ->count('campaign_id');

                $lastWhatsappCampaign = WhatsappMessage::where('lead_id', $lead->id)
                    ->where('type', 'campaign')
                    ->whereNotNull('campaign_id')
                    ->latest('sent_at')
                    ->first();

                return [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'product_id' => $lead->product_id,
                    'product_name' => $lead->product?->name,
                    'source' => $lead->source?->name,
                    'stats' => [
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
                    ],
                ];
            });

        return response()->json([
            'leads' => $leads,
            'totals' => $this->calculateTotals(),
        ]);
    }

    public function totals(Request $request)
    {
        return response()->json($this->calculateTotals());
    }

    private function calculateTotals()
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
}