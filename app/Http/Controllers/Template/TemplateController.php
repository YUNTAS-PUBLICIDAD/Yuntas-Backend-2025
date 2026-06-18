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

               'context' => 'required|string',

               'active' => 'boolean',

               // =========================
               // STEPS
               // =========================

               'steps' => 'array',

               'steps.*.step' => 'required|integer|min:1',

               'steps.*.delay_value' => 'required|integer|min:0',

               'steps.*.delay_unit' => 'required|in:minutes,hours,days',

               'steps.*.active' => 'boolean',

               // =========================
               // VARIANTS
               // =========================

               'steps.*.variants' => 'array',

               'steps.*.variants.*.channel'
                   => 'required|in:whatsapp,email',

               'steps.*.variants.*.subject'
                   => 'nullable|string',

               'steps.*.variants.*.content'
                   => 'nullable|string',

               'steps.*.variants.*.variables'
                   => 'array',

               'steps.*.variants.*.cta_text'
                   => 'nullable|string|max:255',

               'steps.*.variants.*.cta_url'
                   => 'nullable|string|max:500',

               'steps.*.variants.*.active'
                   => 'boolean',

               // =========================
               // ASSETS
               // =========================

               'steps.*.variants.*.assets'
                   => 'array',

               'steps.*.variants.*.assets.*.key'
                   => 'required|string',

               'steps.*.variants.*.assets.*.path'
                   => 'required|string',

               'steps.*.variants.*.assets.*.meta'
                   => 'nullable',

               // =========================
               // PRODUCT OVERRIDES
               // =========================

               'steps.*.variants.*.product_overrides'
                   => 'array',

               'steps.*.variants.*.product_overrides.*.product_id'
                   => 'required|exists:products,id',

               'steps.*.variants.*.product_overrides.*.subject'
                   => 'nullable|string',

               'steps.*.variants.*.product_overrides.*.content'
                   => 'nullable|string',

               'steps.*.variants.*.product_overrides.*.cta_text'
                   => 'nullable|string',

               'steps.*.variants.*.product_overrides.*.cta_url'
                   => 'nullable|string|max:500',
                   'steps.*.variants.*.product_overrides.*.assets'
                       => 'array',

                   'steps.*.variants.*.product_overrides.*.assets.*.key'
                       => 'required|string',

                   'steps.*.variants.*.product_overrides.*.assets.*.path'
                       => 'required|string',

                   'steps.*.variants.*.product_overrides.*.assets.*.meta'
                       => 'nullable',

           ]);
      }
}
