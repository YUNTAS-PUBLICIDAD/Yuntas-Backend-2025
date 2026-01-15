<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailProducto;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ProductMailing1;
use App\Application\Services\CRM\LeadService;
use App\Application\DTOs\CRM\LeadDTO;

class EmailCampanaController extends Controller
{   
    public function __construct(
        private LeadService $leadService
    ) {}

    // Envia un email de campaña a un lead específico
    public function enviar(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'message' => 'nullable|string',
            'producto_id' => 'nullable|integer',
            'source_id' => 'nullable|integer',
        ]);

        $productoId = $request->producto_id;

        // Buscar o crear el lead
        $lead = Lead::where('email', $request->email)->first();

        if (!$lead) {
            // Crear nuevo lead
            $dto = LeadDTO::fromRequest($request);
            $lead = $this->leadService->create($dto);

            Log::info('Nuevo lead creado', [
                'lead_id' => $lead->id,
                'email' => $lead->email,
            ]);
        } else {
            Log::info('Lead existente encontrado', [
                'lead_id' => $lead->id,
                'email' => $lead->email,
            ]);
        }

        $leadId = $request->lead_id;

        // Obtener plantillas según el producto
        if ($productoId) {
            $secciones = EmailProducto::where('producto_id', $productoId)
                ->orderBy('paso')
                ->get();

            if ($secciones->isEmpty()) {
                Log::warning('No hay plantillas para el producto, usando genérica', [
                    'producto_id' => $productoId,
                ]);
                $secciones = $this->obtenerPlantillaGenerica();
            }
        } else {
            Log::info('Sin producto_id, usando plantilla genérica');
            $secciones = $this->obtenerPlantillaGenerica();
        }

        // Enviar correos al lead
        $this->enviarCorreosALead($lead, $secciones);

        return response()->json([
            'message' => 'Campaña enviada correctamente al lead',
            'lead' => [
                'id' => $lead->id,
                'nombre' => $lead->name,
                'email' => $lead->email,
            ],
            'total_correos' => $secciones->count(),
            'tipo_plantilla' => $productoId ? 'producto' : 'genérica'
        ]);
    }

    // Envia un email de campaña a los leads asociados a un producto
    public function enviarCampana(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
        ]);

        $productoId = $request->producto_id;

        // Obtener plantillas del producto
        $secciones = EmailProducto::where('producto_id', $productoId)
            ->orderBy('paso')
            ->get();

        if ($secciones->isEmpty()) {
            Log::warning('⚠️ No hay plantillas para el producto', [
                'producto_id' => $productoId,
            ]);

            return response()->json([
                'message' => 'No existe plantilla para este producto'
            ], 422);
        }

        $leads = Lead::where('product_id', $productoId)
            ->whereNotNull('email')
            ->get();

        if ($leads->isEmpty()) {
            Log::warning('⚠️ No hay leads para el producto', [
                'producto_id' => $productoId,
            ]);

            return response()->json([
                'message' => 'No existen leads para este producto'
            ], 422);
        }

        // Envío de correos
        foreach ($leads as $lead) {
            $this->enviarCorreosALead($lead, $secciones);
        }

        return response()->json([
            'message' => 'Campaña enviada correctamente',
            'total_leads' => $leads->count(),
            'total_correos' => $leads->count() * $secciones->count()
        ]);
    }

    private function enviarCorreosALead($lead, $secciones)
    {
        $cliente = [
            'nombre' => $lead->name,
            'correo' => $lead->email,
            'telefono' => $lead->phone,
        ];

        foreach ($secciones as $seccion) {
            Log::info('Enviando sección', [
                'email' => $lead->email,
                'paso' => $seccion->paso,
                'titulo' => $seccion->titulo,
            ]);

            Mail::to($lead->email)->send(
                new ProductMailing1($seccion, $cliente)
            );
        }
    }

    private function obtenerPlantillaGenerica()
    {
        // Se tienen que implementar la plantilla generaica (lo de abajo es por mientras)
        return EmailProducto::whereNull('producto_id')
            ->orderBy('paso')
            ->get();
    }
}
