<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailMessage;
use App\Models\WhatsappMessage;
use App\Models\Lead;
use Illuminate\Http\Request;

class MessageStatsController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::with(['emailMessages', 'whatsappMessages'])
            ->get()
            ->map(function ($lead) {
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
                                'total' => $lead->emailMessages()->popup()->count(),
                                'exitosos' => $lead->emailMessages()->popup()->success()->count(),
                                'fallidos' => $lead->emailMessages()->popup()->failed()->count(),
                                'ultimo_envio' => $lead->emailMessages()->popup()->latest('sent_at')->first()?->sent_at,
                            ],
                            'campaign' => [
                                'total' => $lead->emailMessages()->campaign()->count(),
                                'exitosos' => $lead->emailMessages()->campaign()->success()->count(),
                                'fallidos' => $lead->emailMessages()->campaign()->failed()->count(),
                                'ultimo_envio' => $lead->emailMessages()->campaign()->latest('sent_at')->first()?->sent_at,
                            ],
                        ],
                        'whatsapp' => [
                            'popup' => [
                                'total' => $lead->whatsappMessages()->popup()->count(),
                                'exitosos' => $lead->whatsappMessages()->popup()->success()->count(),
                                'fallidos' => $lead->whatsappMessages()->popup()->failed()->count(),
                                'ultimo_envio' => $lead->whatsappMessages()->popup()->latest('sent_at')->first()?->sent_at,
                            ],
                            'campaign' => [
                                'total' => $lead->whatsappMessages()->campaign()->count(),
                                'exitosos' => $lead->whatsappMessages()->campaign()->success()->count(),
                                'fallidos' => $lead->whatsappMessages()->campaign()->failed()->count(),
                                'ultimo_envio' => $lead->whatsappMessages()->campaign()->latest('sent_at')->first()?->sent_at,
                            ],
                        ],
                    ],
                ];
            });

        return response()->json([
            'leads' => $leads,
            'totals' => [
                'email' => [
                    'popup' => [
                        'total' => EmailMessage::popup()->count(),
                        'exitosos' => EmailMessage::popup()->success()->count(),
                        'fallidos' => EmailMessage::popup()->failed()->count(),
                    ],
                    'campaign' => [
                        'total' => EmailMessage::campaign()->count(),
                        'exitosos' => EmailMessage::campaign()->success()->count(),
                        'fallidos' => EmailMessage::campaign()->failed()->count(),
                    ],
                ],
                'whatsapp' => [
                    'popup' => [
                        'total' => WhatsappMessage::popup()->count(),
                        'exitosos' => WhatsappMessage::popup()->success()->count(),
                        'fallidos' => WhatsappMessage::popup()->failed()->count(),
                    ],
                    'campaign' => [
                        'total' => WhatsappMessage::campaign()->count(),
                        'exitosos' => WhatsappMessage::campaign()->success()->count(),
                        'fallidos' => WhatsappMessage::campaign()->failed()->count(),
                    ],
                ],
            ],
        ]);
    }

    // Stats globales (resumen general)
    public function totals(Request $request)
    {
        return response()->json([
            'email' => [
                'popup' => [
                    'total' => EmailMessage::popup()->count(),
                    'exitosos' => EmailMessage::popup()->success()->count(),
                    'fallidos' => EmailMessage::popup()->failed()->count(),
                    'ultimo_envio' => EmailMessage::popup()->latest('sent_at')->first()?->sent_at,
                ],
                'campaign' => [
                    'total' => EmailMessage::campaign()->count(),
                    'exitosos' => EmailMessage::campaign()->success()->count(),
                    'fallidos' => EmailMessage::campaign()->failed()->count(),
                    'ultimo_envio' => EmailMessage::campaign()->latest('sent_at')->first()?->sent_at,
                ],
            ],
            'whatsapp' => [
                'popup' => [
                    'total' => WhatsappMessage::popup()->count(),
                    'exitosos' => WhatsappMessage::popup()->success()->count(),
                    'fallidos' => WhatsappMessage::popup()->failed()->count(),
                    'ultimo_envio' => WhatsappMessage::popup()->latest('sent_at')->first()?->sent_at,
                ],
                'campaign' => [
                    'total' => WhatsappMessage::campaign()->count(),
                    'exitosos' => WhatsappMessage::campaign()->success()->count(),
                    'fallidos' => WhatsappMessage::campaign()->failed()->count(),
                    'ultimo_envio' => WhatsappMessage::campaign()->latest('sent_at')->first()?->sent_at,
                ],
            ],
        ]);
    }
}