<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailProducto extends Model
{
    public static $apiUrl = 'https://hollis-unturbid-ying.ngrok-free.dev';

    protected $fillable = [
        'producto_id',
        'paso',
        'titulo',
        'parrafo1',
        'imagen_principal',
        'imagenes_secundarias',
    ];

    public function messages()
{
    return $this->hasMany(EmailMessage::class);
}

    protected $casts = [
        'imagenes_secundarias' => 'array',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public static function buildImageUrl($value)
    {
        if (!$value) {
            return null;
        }

        return preg_match('/^https?:\/\//', $value)
            ? $value
            : self::$apiUrl . $value;
    }
    
}
