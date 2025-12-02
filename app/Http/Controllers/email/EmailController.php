<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SendEmailRequest;
use App\Application\Services\Support\SendEmailService;
use App\Application\DTOs\Support\SendEmailDTO;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    public function __construct(
        private SendEmailService $service
    ) {}

    public function send(SendEmailRequest $request): JsonResponse
    {
        try {
            $dto = SendEmailDTO::fromRequest($request);
            $this->service->send($dto);

            return response()->json([
                'success' => true,
                'message' => 'Email enviado correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
