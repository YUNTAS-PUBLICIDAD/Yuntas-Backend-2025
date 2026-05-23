<?php

namespace App\Application\Services\Chatbot\Flows;

use App\Application\Services\Chatbot\Context\ChatContext;
use App\Application\Services\Chatbot\Extractors\BusinessTypeExtractor;
use App\Application\Services\Chatbot\Extractors\InstallationExtractor;
use App\Application\Services\Product\ProductRecommendationService;

class ProductDiscoveryFlow
{
  public function handle(string $message, ChatContext $context):array
  {
    $step = data_get($context->data, 'flow.step');

    return match ($step) {
      'installation' => $this->handleInstallation($message, $context),
      'business' => $this->handleBusiness($message, $context),
      default => $this->finish($context)
    };
  }

  protected function handleInstallation(
         string $message,
         ChatContext $context
     ): array {

         // primera vez
         if (empty($message)) {

             return [
                 'text' => 'Perfecto. ¿La usarás en interior o exterior?',
                 'metadata' => null
             ];
         }

         $installation = app(InstallationExtractor::class)
             ->extract($message);

         if (!$installation) {

             return [
                 'text' => '¿Será para interior o exterior?',
                 'metadata' => null
             ];
         }

         data_set(
             $context->data,
             'entities.installation',
             $installation
         );

         data_set(
             $context->data,
             'flow.step',
             'business'
         );

         return [
             'text' => 'Entiendo. ¿Para qué tipo de negocio la necesitas?',
             'metadata' => null
         ];
     }

     protected function handleBusiness(
         string $message,
         ChatContext $context
     ): array {

         $business = app(BusinessTypeExtractor::class)
             ->extract($message);

         if (!$business) {

             return [
                 'text' => '¿Qué tipo de negocio tienes?',
                 'metadata' => null
             ];
         }

         data_set(
             $context->data,
             'entities.business',
             $business
         );

         data_set(
         $context->data,
         'entities.business_raw',
         $message
         );

         return $this->finish($context);
     }

     protected function finish(ChatContext $context): array
     {
         $entities = data_get(
             $context->data,
             'entities',
             []
         );

         $products = app(ProductRecommendationService::class)
             ->recommend($entities);

         $productName = data_get(
             $entities,
             'product_name'
         );

         $installation = data_get(
             $entities,
             'installation'
         );

         // $business = data_get(
         //     $entities,
         //     'business'
         // );
         $business = data_get(
         $entities,
         'business_raw'
         );

         $whatsappMessage = rawurlencode(
             "Hola, me interesa {$productName}. "
             ."Uso: {$installation}. "
             ."Negocio: {$business}."
         );

         data_set($context->data, 'flow', null);

         return [
             'text' => 'Perfecto, estas opciones podrían funcionar muy bien 👇',

             'metadata' => [
                 'type' => 'products',

                 'products' => $products
                     ->values()
                     ->toArray(),

                 'whatsapp_url'
                     => "https://wa.me/51912849782?text={$whatsappMessage}"
             ]
         ];
     }
}
