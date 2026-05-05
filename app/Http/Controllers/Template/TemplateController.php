<?php

namespace App\Http\Controllers\Template;

use App\Application\Services\Template\TemplateService;
use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{

  public function __construct(
          protected TemplateService $service
      ) {}

      public function variables()
      {
        return response()->json(
        $this->service->getAvailableVariables()
        );
      }

      public function index()
      {
          return $this->service->list();
      }

      public function show($id)
      {
          return $this->service->get($id);
      }

      public function store(Request $request)
      {
          $data = $this->validateRequest($request);

          return response()->json(
              $this->service->save($data),
              201
          );
      }

      public function update(Request $request, $id)
      {
          $data = $this->validateRequest($request);

          return $this->service->save($data, (int) $id);
      }

      public function destroy($id)
      {
          return response()->json(
              $this->service->delete((int) $id)
          );
      }

      // =========================
      // VALIDATION CENTRALIZADA
      // =========================
      private function validateRequest(Request $request): array
      {
          return $request->validate([
              'name' => 'required|string|max:255',
              'active' => 'boolean',

              'variants' => 'array',
              'variants.*.channel' => 'required|string|in:whatsapp,email',
              'variants.*.context' => 'required|string',
              'variants.*.subject' => 'nullable|string',
              'variants.*.content' => 'required|string',
              'variants.*.variables' => 'array',
              'variants.*.active' => 'boolean',

              // CTA
              'variants.*.cta_text' => 'nullable|string|max:255',
              'variants.*.cta_url' => 'nullable|url|max:500',

              'variants.*.assets' => 'array',
              'variants.*.assets.*.key' => 'required|string',
              'variants.*.assets.*.path' => 'required|string',
              'variants.*.assets.*.meta' => 'nullable',

              'variants.*.product_assets' => 'array',
              'variants.*.product_assets.*.product_id' => 'required|integer|exists:products,id',
              'variants.*.product_assets.*.key' => 'required|string',
              'variants.*.product_assets.*.path' => 'required|string',
          ]);
      }
}
