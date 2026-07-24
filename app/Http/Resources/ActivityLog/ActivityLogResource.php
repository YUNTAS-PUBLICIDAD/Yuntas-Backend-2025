<?php

namespace App\Http\Resources\ActivityLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'action' => $this->description,

            'section' => match ($this->log_name) {
                'Blog' => 'Blogs',
                'Product' => 'Productos',
                'Claim' => 'Reclamaciones',
                'Contact' => 'Contacto',
                'Popup' => 'Popups',
                'Template' => 'Plantillas',
                default => $this->log_name,
            },

            'date' => $this->created_at->format('d/m/Y'),

            'time' => $this->created_at->format('H:i'),
        ];
    }
}