<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="TemplateContent",
 *     type="object",
 *     required={"channel","content","active"},
 *     
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="channel", type="string", example="whatsapp"),
 *     @OA\Property(property="subject", type="string", example="Promoción"),
 *     @OA\Property(property="content", type="string", example="Hola {{name}}"),
 *     @OA\Property(
 *         property="variables",
 *         type="array",
 *         @OA\Items(type="string")
 *     ),
 *     @OA\Property(property="image_url", type="string", example="/storage/plantillas/img.png"),
 *     @OA\Property(property="active", type="boolean", example=true)
 * )
 */
class TemplateContentSchema {}