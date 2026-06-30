<?php

namespace App\Application\Services\Template;

use App\Application\Support\TemplateVariableBuilder;
use App\Models\Lead;
use App\Models\TemplateVariant;

// Solo renderiza contenido dinámico
class TemplateRenderService
{
    public function render(
        Lead $lead,
        TemplateVariant $variant
    ): array {

        $variables =
            TemplateVariableBuilder::forLead($lead);

            // =====================================
                  // PRODUCT OVERRIDE
                  // =====================================

                  $override = null;

                  if ($lead->product_id) {

                      $override = $variant
                          ->productOverrides
                          ->where(
                              'product_id',
                              $lead->product_id
                          )
                          ->first();
                  }

                  // =====================================
                  // CONTENT
                  // =====================================

                  $content =
                      (!is_null($override?->content) && $override->content !== '')
                      ? $override->content
                      : ($variant->content ?? '');

                  $subject =
                      (!is_null($override?->subject) && $override->subject !== '')
                      ? $override->subject
                      : $variant->subject;

                  $ctaText =
                      (!is_null($override?->cta_text) && $override->cta_text !== '')
                      ? $override->cta_text
                      : $variant->cta_text;

                  $ctaUrl =
                      (!is_null($override?->cta_url) && $override->cta_url !== '')
                      ? $override->cta_url
                      : $variant->cta_url;

                  return [

                      'to' => $variant->channel === 'email'
                          ? $lead->email
                          : $lead->phone,

                      'subject' => $subject,

                      'content' => $this->replaceVariables(
                          $content,
                          $variables
                      ),

                      'image_url' => $this->resolveImage(
                          $variant,
                          $lead->product_id
                      ),

                      'cta_text' => $ctaText,

                      'cta_url' => $ctaUrl,
                  ];

    }

    // =====================================================
     // VARIABLES
     // =====================================================

     protected function replaceVariables(
         string $content,
         array $variables
     ): string {

         foreach ($variables as $key => $value) {

             $content = str_replace(
                 "{{{$key}}}",
                 $value,
                 $content
             );
         }

         return $content;
     }

     // =====================================================
     // IMAGE
     // =====================================================

     protected function resolveImage(
         TemplateVariant $variant,
         ?int $productId = null
     ): ?string {

         // =====================================
         // PRODUCT OVERRIDE IMAGE
         // =====================================

         if ($productId) {

             $override = $variant
                 ->productOverrides
                 ->where(
                     'product_id',
                     $productId
                 )
                 ->first();

             if (
                 $override &&
                 !empty($override->assets)
             ) {

                 $image = collect(
                     $override->assets
                 )->firstWhere(
                     'key',
                     'image'
                 );

                 if ($image) {
                     return asset(
                         $image['path']
                     );
                 }
             }
         }

         // =====================================
         // GLOBAL IMAGE
         // =====================================

         $asset = $variant
             ->assets
             ->where('key', 'image')
             ->first();

         if ($asset) {
             return asset($asset->path);
         }

         return null;
     }
}
