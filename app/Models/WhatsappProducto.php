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
    ];

    public function messages() {
        return $this->hasMany(WhatsappMessage::class);
    }

    public function producto() {
        return $this->belongsTo(Producto::class);
    }
}
