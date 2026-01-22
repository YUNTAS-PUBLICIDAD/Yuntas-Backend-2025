<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappPopup extends Model
{
    protected $table = 'whatsapp_popup';

    protected $fillable = [
        'lead_source_id',
        'nombre',
        'mensaje',
        'imagen_url',
        'variables',
        'activo'
    ];

    protected $casts = [
        'variables' => 'array',
        'activo' => 'boolean',
    ];

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class);
    }

    // Procesar variables dinámicas
    public function procesarVariables(array $datos): string
    {
        $mensaje = $this->mensaje;
        
        if (!$this->variables) {
            return $mensaje;
        }

        foreach ($this->variables as $variable) {
            if (isset($datos[$variable])) {
                $mensaje = str_replace("{{$variable}}", $datos[$variable], $mensaje);
            }
        }
        
        return $mensaje;
    }

    // Scope para obtener plantilla activa por source
    public function scopeActivaPorSource($query, $sourceId)
    {
        return $query->where('lead_source_id', $sourceId)
                    ->where('activo', true)
                    ->first();
    }
}