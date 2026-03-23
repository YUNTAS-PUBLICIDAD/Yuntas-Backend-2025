<?php
namespace App\Http\Controllers\Email;

use App\Application\Services\Template\TemplateService;
use App\Http\Controllers\Controller;
use App\Jobs\SendTemplateEmailJob;
use Illuminate\Http\Request;
use App\Jobs\SendProductEmailJob;
use Illuminate\Support\Facades\Mail;
use App\Mail\InicioMailing;
use App\Mail\ProductosMailing;
use App\Models\Lead;
use App\Models\EmailMessage;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isNull;

class EmailPopupController extends Controller
{
  protected TemplateService $templateService;

  public function __construct(TemplateService $templateService)
  {
    $this->templateService = $templateService;
  }

    public function enviar(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'message' => 'nullable|string',
            'product_id' => 'nullable|integer',
            'source_id' => 'required|integer',
        ]);

        // Crear o actualizar lead
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

        // $cliente = [
        //     'nombre' => $lead->name,
        //     'correo' => $lead->email,
        //     'telefono' => $lead->phone,
        // ];

        // Cargar relación
        $lead->loadMissing('product');

        $data = [
          'nombre' => $lead->name,
          'email' => $lead->email,
          'telefono' => $lead->phone,
          'fecha' => now()->format('Y-m-d'),
          'hora' => now()->format('H:i'),

          'producto_nombre' => $lead->product->name ?? '',
          'descripcion' => $lead->product->description ?? ''
        ];

        Log::info('EMAIL FLOW DEBUG', [
          'source_id' => $lead->source_id,
          'product_id' => $lead->product_id,
          'data' => $data
        ]);


        // enviar si viene de inicio o producto
        // CASO 1: POPUP (sin producto)
        // if (isNull($lead->product_id)) {
            // if ($lead->source->name === 'Inicio') {
                try {
                    // Mail::to($cliente['correo'])->send(new InicioMailing($cliente));
                    $template = $this->templateService->render(
                      $lead->source_id,
                      'email',
                      $data
                    );

                    // Log para inspección
                    Log::info('Template Email renderizado', [
                      'source_id' => $lead->source_id,
                      'variables' => $data,
                      'template' => $template
                    ]);
                    $imagenUrl = null;
      
                    // prioridad 1: producto
                    if ($lead->product_id && $lead->product) {
                      $lead->product->loadMissing('mainImage');

                      if ($lead->product->mainImage?->url) {
                        $imagenUrl = asset($lead->product->mainImage->url);
                      }
                    }

                    // prioridad 2: template
                    if (!$imagenUrl && !empty($template['image_url'])) {
                      $imagenUrl = $template['image_url'];
                    }
                    Log::info('DEBUG IMAGEN', [
                      'image_url_template'  => $template['image_url']
                    ]);


                    // Mail::raw($template['message'], function ($message) use ($template, $lead){
                    //   $message->to($lead->email)
                    //   ->subject($template['subject'] ?? 'Mensaje');
                    // });
                    // Mail::send([], [], function ($message) use ($template, $lead){
                    //   $message->to($lead->email)
                    //           ->subject($template['subject'] ?? 'Mensaje')
                    //           ->setBody($template['message'], 'text/html');
                    // });
                    
                    // $html = $template['message'];

                    // if(!empty($template['image_url'])){
                    //     $html = '<img src="'.$template['image_url'].'" style="max-width:100%;"><br>' . $html;
                    // }
    //                 if ($imagenUrl) {
    // $html = '<img src="'.$imagenUrl.'" style="max-width:100%;"><br>' . $html;
// }
$html = view('emails.layouts.base', [
  'contenido' => $template['message'],
  'imagenUrl' => $imagenUrl
])->render();
                    
                    Mail::send([], [], function ($message) use ($template, $lead, $html){
                      $message->to($lead->email)
                      ->subject($template['subject'] ?? 'Mensaje')
                      // ->html($template['message']); 
                      ->html($html);
                    });

                    // Guardar registro
                    EmailMessage::create([
                        'lead_id' => $lead->id,
                        'type' => 'popup',
                        // 'subject' => 'Bienvenido a Yuntas',
                        'subject' => $template['subject'] ?? null,
                        // 'body' => 'Email de bienvenida desde Inicio',
                        // 'body' => $template['message'],
                        'body' => $html,
                        'status' => 'enviado',
                        'sent_at' => now(),
                    ]);

                    return response()->json([
                        'message' => 'Email de Inicio enviado correctamente',
                        'lead_id' => $lead->id,
                    ], 200);
                    
                } catch (\Exception $e) {
                    EmailMessage::create([
                        'lead_id' => $lead->id,
                        'type' => 'popup',
                        // 'subject' => 'Bienvenido a Yuntas',
                        'subject' => 'Error',
                        // 'body' => 'Email de bienvenida desde Inicio',
                        'body' => 'Fallo al enviar email',
                        'status' => 'fallido',
                        'sent_at' => now(),
                        'error_message' => $e->getMessage(),
                    ]);
                    // throw $e;
                    return response()->json([
                      'message' => $e->getMessage(),
                    ], 500);
                }
            // } 
            // CASO 2: Producto
            // try {
            //   $product = $lead->product;
            //   $data['producto_nombre'] = $product->name ?? 'Producto';
            //   $data['descripcion'] = $product->description ?? 'Sin descripción';
            //   $data['fecha'] = now()->translatedFormat('d \\d\\e F \\d\\e Y');
            //   $data['hora'] = now()->format('h:i A');
            //   // $detailSourceId = $lead->source_id;
            //   $template = $this->templateService->render($lead->product_id, 'email', $data);
            //   // Mail::raw($template['message'], function ($message) use ($template, $lead){
            //   //   $message->to($lead->email)
            //   //   ->subject($template['subject'] ?? 'Producto');
            //   // });
            //   // Mail::send([], [], function ($message) use ($template, $lead){
            //   //   $message->to($lead->email)
            //   //   ->subject($template['subject'] ?? 'Producto')
            //   //   ->setBody($template['message'], 'text/html');
            //   // });
            //   Mail::send([], [], function ($message) use ($template, $lead) {
            //     $message->to($lead->email)
            //     ->subject($template['subject'] ?? 'Producto')
            //     ->html($template['message']);
            //   });

            //   EmailMessage::create([
            //     'lead_id' => $lead->id,
            //     'type' => 'popup',
            //     'subject' => $template['subject'] ?? null,
            //     'body' => $template['message'],
            //     'status' => 'enviado',
            //     'sent_at' => now()
            //   ]);

            //   return response()->json([
            //     'message' => 'Email de producto enviado',
            //     'lead_id' => $lead->id
            //   ], 200);
            // } catch (\Throwable $e) {
            //   return response()->json([
            //     'message' => $e->getMessage(),
            //   ],500);
            // }
    
            // else if ($lead->source->name === 'Productos') {
            //     try {
            //         Mail::to($cliente['correo'])->send(new ProductosMailing($cliente));

            //         EmailMessage::create([
            //             'lead_id' => $lead->id,
            //             'type' => 'popup',
            //             'subject' => 'Bienvenido a Yuntas',
            //             'body' => 'Email de productos',
            //             'status' => 'enviado',
            //             'sent_at' => now(),
            //         ]);

            //         return response()->json([
            //             'message' => 'Email de Productos enviado correctamente',
            //             'lead_id' => $lead->id,
            //         ], 200);
            //     } catch (\Exception $e) {
            //         EmailMessage::create([
            //             'lead_id' => $lead->id,
            //             'type' => 'popup',
            //             'subject' => 'Bienvenido a Yuntas',
            //             'body' => 'Email de productos',
            //             'status' => 'fallido',
            //             'sent_at' => now(),
            //             'error_message' => $e->getMessage(),
            //         ]);
            //         throw $e;
            //     }
            // } else {
            //     Log::info('Lead no viene de Inicio ni Productos, no se envía email', [
            //         'lead_id' => $lead->id,
            //         'source' => $lead->source->name,
            //     ]);
            //     return response()->json([
            //         'message' => 'Lead registrado sin envío de email',
            //         'lead_id' => $lead->id,
            //     ], 200);
        //     }
        // }

        // $productoId = $lead->product_id;

        // Email inmediato (Día 0)
        // SendProductEmailJob::dispatch($productoId, 0, $cliente);
        // SendProductEmailJob::dispatch($productoId, 0, $data);
        // Día 0
        // SendTemplateEmailJob::dispatch($lead->id, $lead->source_id, 'email', $data);

        // EMAIL INMEDIATO (step 0)
//         $template = $this->templateService->renderByProduct(
//           $lead->product_id,
//           0,
//           'email',
//           $data
//         );
//         Mail::raw($template['message'], function ($message) use ($template, $lead){
// $message->to($lead->email)
// ->subject($template['subject'] ?? 'Producto');
//         });
//         SendTemplateEmailJob::dispatch(
//           $lead->id,
//           $lead->product_id,
//           0,
//           'email',
//           $data
//         );

        // Día 1
        // SendProductEmailJob::dispatch($productoId, 1, $cliente)
        //     ->delay(now()->addDays(1));
        // SendProductEmailJob::dispatch($productoId, 1, $data)->delay(now()->addDays(1));
        // SendTemplateEmailJob::dispatch($lead->id, $lead->source_id, 'email', $data)->delay(now()->addDays(1));
        // SendTemplateEmailJob::dispatch(
        //   $lead->id,
        //   $lead->product_id,
        //   1,
        //   'email',
        //   $data
        // )->delay(now()->addDays(1));

        // Día 3
        // SendProductEmailJob::dispatch($productoId, 2, $cliente)
        //     ->delay(now()->addDays(3));
        // SendProductEmailJob::dispatch($productoId, 2, $data)->delay(now()->addDays(3));
        // SendTemplateEmailJob::dispatch($lead->id, $lead->source_id, 'email', $data)->delay(now()->addDays(3));
        // SendTemplateEmailJob::dispatch(
        //   $lead->id,
        //   $lead->product_id,
        //   2,
        //   'email',
        //   $data
        // )->delay(now()->addDays(3));

        // return response()->json([
        //     'message' => 'Secuencia de emails programada correctamente',
        //     'lead_id' => $lead->id,
        //     // 'total_correos_programados' => 3
        // ], 200);
    }
}