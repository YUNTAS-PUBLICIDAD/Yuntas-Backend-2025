<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappProducto extends Model
{
    protected $table = 'whatsapp_producto';
    
    protected $fillable = [
        'parrafo',
        'imagen_principal',
        'producto_id',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function messages() {
        return $this->hasMany(WhatsappMessage::class);
    }

    public function producto() {
        return $this->belongsTo(Producto::class);
    }

    // obtener la plantilla por defecto
    public static function getDefault()
    {
        return self::where('is_default', true)->first();
    }

    // para establecer la plantilla como por defecto
    public function setAsDefault()
    {
        self::whereNull('producto_id')
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
