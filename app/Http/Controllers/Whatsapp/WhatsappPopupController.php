<?php

namespace App\Http\Controllers\Whatsapp;

use App\Application\Services\Template\TemplateService;
use App\Application\Support\TemplateVariableBuilder;
use App\Http\Controllers\Controller;
use App\Models\WhatsappMessage;
use App\Models\Lead;
// use App\Models\WhatsappPopup;
// use App\Models\Product;
// use App\Models\WhatsappProducto;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsappPopupController extends Controller
{
    private $whatsappServiceUrl;
    private $templateService;

    public function __construct(TemplateService $templateService) {
        $this->whatsappServiceUrl = env('WHATSAPP_SERVICE_URL', 'http://localhost:3001');
        $this->templateService = $templateService;
    }

    // Enviar mensaje de WhatsApp a un lead específico
    public function enviar(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'message' => 'nullable|string',
            'product_id' => 'nullable|integer',
            'source_id' => 'required|integer',
        ]);

        // Buscar o crear lead
        $leadExistente = Lead::where('email', $request->email)->first();
        $phoneChanged = $leadExistente && $leadExistente->phone !== $request->phone;

        $lead = Lead::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'message' => $request->message,
                'product_id' => $request->product_id,
                'source_id' => $request->source_id,
            ]
        );

        if ($phoneChanged) { // si phone cambia, se limpia chat_id en mensajes de WhatsApp
            WhatsappMessage::where('lead_id', $lead->id)
                ->whereNotNull('chat_id')
                ->update(['chat_id' => null]);
        }

        // Validar que tenga teléfono
        if (!$lead->phone) {
            return response()->json([
                'success' => false,
                'message' => 'El lead no tiene número de teléfono'
            ], 422);
        }

        // Obtener plantilla según el popup del que viene el lead
        // $plantillaPopup = WhatsappPopup::activaPorSource($request->source_id);
        // // variables para el mensaje
        // $variables = [
        //     'nombre' => $lead->name,
        // ];
        $variables = TemplateVariableBuilder::forLead($lead);

        // if (!$plantillaPopup) {
          //     return response()->json([
            //         'success' => false,
            //         'message' => 'No hay plantilla configurada para esta fuente'
            //     ], 500);
            // }


            // Si es detalle de producto, agregar variables y obtener imagen
            // LÓGICA DE PRODUCTO
            $imagenUrl = null;

            // CONTEXTO: PRODUCTO DETALLE
            if ($lead->product_id) {
              // $imagenUrl = $lead->product;
              // ->where('slot_id', 5) // Slot de imagen principal
              // ->first()?->url;
              $product = $lead->product;
              if (!$product) {
               return response()->json([
                'success' => false,
                'message' => 'Producto no encotrado'
               ], 404);
              }

              // variables adicionales
              // $variables = array_merge($variables, [
              //   'producto_nombre' => $lead->product->name ?? '',
              //   'descripcion' => $lead->product->description ?? '',
              //   'fecha' => now('America/Lima')->format('d/m/Y'),
              //   'hora' => now('America/Lima')->format('H:i'),
              //   'email' => $lead->email,
              //   ]);
              //   }
              // $variables = array_merge($variables, [

              //   // 'nombre' => $lead->name,
              //   'producto_nombre' => $product->name ?? '',
              //   'descripcion' => $product->description ?? '',
              //   'fecha' => now('America/Lima')->format('d/m/Y'),
              //   'hora' => now('America/Lima')->format('H:i'),
              //   'email' => $lead->email,
              // ]);
              // }

                // Imagen dinámica del producto
                // $imagenUrl = optional($product->images->where('slot_id', 5)->first())->url();
                // $imagenUrl = optional($product?->images?->firstWhere('slot_id', 5))->url ?? null;
                // else { // Para popup de Inicio y Productos, se usa imagen de la plantilla
                //     $imagenUrl = $plantillaPopup->imagen_url;
                // }
                // $product->loadMissing('images');
                // $imagen = $product->images->firstWhere('slot_id', 5);
                // $imagenUrl = $imagen?->url;
                $product->loadMissing('mainImage');
                $imagenUrl = $product->mainImage?->url;
            }
                // Render Template
                try {

                // Render template
                  // $templateData = $this->templateService->render(
                  //   $request->source_id,
                  //   'whatsapp',
                  //   $variables
                  // );
                  Log::info('FLOW DEBUG', [
 'product_id'  => $lead->product_id,
 'tiene_producto' => (bool) $lead->product,
 'variables' => $variables
                  ]);

                  $templateData = $this->templateService->render(
                    $request->source_id,
                  'whatsapp',
                  $variables
                  );


                  Log::info('Template renderizado correctamente', [
                    'source_id' => $request->source_id,
                    'variables_keys' => array_keys($variables)
                    // 'variables' => $variables, 'template' => $templateData
                  ]);

                } catch (Exception $e) {
                  Log::error('Error al renderizar template', [

                  'source_id' => $request->source_id,
                  'variables' => $variables,
                  'error' => $e->getMessage()
                  ]);
                  return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                  ], 500);
                }

                // if(!$templateData){
                //   return response()->json([
                //     'success' => false,
                //     'message' => 'No hay template configurado'
                //   ], 500);
                // }

                $mensaje = $templateData['message'];

                // Fallback solo si no hay imagen de producto
                if (!$imagenUrl) {
                  $imagenUrl = $templateData['image_url'];
                }

        // Enviar mensaje al lead
        // $resultado = $this->enviarWhatsappALead(
        //     $lead,
        //     $plantillaPopup,
        //     $variables,
        //     $imagenUrl
        // );

        // Enviar
        $resultado = $this->enviarWhatsappALead($lead, $mensaje, $imagenUrl);

        if (!$resultado['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar mensaje de WhatsApp'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mensaje de WhatsApp enviado correctamente',
            'lead' => [
                'id' => $lead->id,
                'nombre' => $lead->name,
                'phone' => $lead->phone,
            ]
        ]);
    }

    private function enviarWhatsappALead(
      $lead,
      // $plantilla,
      string $mensaje,
      ?string $imagenUrl
      // array $variables,
      // ?string $imagenProducto
      )
    {
        try {

            // $mensaje = $plantilla->procesarVariables($variables);
            // $imagenUrl = $imagenProducto;

            // Verificar si podemos omitir validación en el servicio de WhatsApp
            $ultimoMensajeExitoso = WhatsappMessage::where('lead_id', $lead->id)
                ->where('status', 'enviado')
                ->latest('sent_at')
                ->first();

            $skipValidation = false;
            $chatId = null;

            if ($ultimoMensajeExitoso) {
                // Verificar que no haya fallidos después del último exitoso
                $hayFallidosRecientes = WhatsappMessage::where('lead_id', $lead->id)
                    ->where('status', 'fallido')
                    ->where('sent_at', '>', $ultimoMensajeExitoso->sent_at)
                    ->exists();

                if (!$hayFallidosRecientes) {
                    $skipValidation = true;
                    $chatId = $ultimoMensajeExitoso->chat_id;
                }
            }

            // Preparar payload base
            $payload = [
                'phone' => strlen($lead->phone) === 9 ? '51' . $lead->phone : $lead->phone,
            ];

            if ($skipValidation) {
                $payload['skipValidation'] = true;
                if ($chatId) {
                    $payload['chatId'] = $chatId;
                }
            }

            // Si tiene imagen, enviar con imagen
            if ($imagenUrl) {
                // $imagePath = str_replace('storage/', '', $imagenUrl);
                $parsedPath = parse_url($imagenUrl, PHP_URL_PATH);
                $imagePath = ltrim(str_replace('/storage/', '', $parsedPath), '/');
                $image = Storage::disk('public')->get($imagePath);

                $payload['imageData'] = base64_encode($image);
                $payload['caption'] = $mensaje;

                Log::info('Enviando WhatsApp con imagen', [
                    'lead_id' => $lead->id,
                    'phone' => $payload['phone'],
                    'tiene_imagen' => true,
                    'imagePath' => $imagePath
                ]);

                // $response = Http::timeout(30)->post("{$this->whatsappServiceUrl}/api/whatsapp/send-image", $payload);

                $response = Http::timeout(10)->retry(3, 1000, function ($exception, $request){
                  if($exception instanceof ConnectionException){
                    Log::warning('Retrying Whatsapp request', [
                      'error' => $exception->getMessage()
                    ]);
                    return true;
                  }

                  if($exception instanceof RequestException){
                    $status = $exception->response?->status();

                    // Retry solo en 5xx (no en 4xx)
                    return $status >= 500;
                  }

                  return false;

                })->post("{$this->whatsappServiceUrl}/api/whatsapp/send-image", $payload);

                Log::info('Response Whatsapp RAW (image)', [
                  'status' => $response->status(),
                  'body' => $response->body(),
                  'json' => $response->json()
                ]);
            } else {
                $payload['message'] = $mensaje;

                Log::info('Enviando WhatsApp solo texto', [
                    'lead_id' => $lead->id,
                    'phone' => $payload['phone']
                ]);

                Log::info('Payload send-message', ['payload' => $payload]);
                $response = Http::timeout(30)->post("{$this->whatsappServiceUrl}/api/whatsapp/send-message", $payload);

                // Log::info('Response raw send-message', [
                //   'body' => $response->body(),
                //   'status' => $response->status(),
                // ]);

                Log::info('Response WhatsApp RAW (text)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'json' => $response->json()
                ]);
            }

            $responseData = $response->json();

            $success = $responseData['success'] ?? false;

            // $success = $response->json()['success'] ?? false;
            $responseChatId = $response->json()['chatId'] ?? null;

            $errorMessage = null;

            if(!$success){
              $error = $responseData['error'] ?? 'Error desconocido';

              // Evitar array -> string
              $errorMessage = is_array($error) ? json_encode($error) : $error;
            }

            // Guardar registro del mensaje
            WhatsappMessage::create([
                'lead_id' => $lead->id,
                'type' => 'popup',
                'body' => $mensaje,
                'status' => $success ? 'enviado' : 'fallido',
                'image_url' => $imagenUrl,
                'sent_at' => now(),
                'chat_id' => $responseChatId,
                // 'error_message' => $success ? null : ($response->json()['message'] ?? 'Error desconocido'),
                'error_message' => $errorMessage
            ]);

            return ['success' => $success];

        } catch (Exception $e) {
            Log::error('Error enviando WhatsApp', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Guardar registro del error
            WhatsappMessage::create([
                'lead_id' => $lead->id,
                'type' => 'popup',
                'body' => $mensaje ?? '',
                'status' => 'fallido',
                'chat_id' => null,
                'image_url' => $imagenUrl ?? null,
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
