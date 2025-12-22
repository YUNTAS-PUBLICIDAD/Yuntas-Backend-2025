<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailProducto extends Model
{


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

        if (preg_match('/^https?:\/\//', $value)) {
            return $value;
        }

        return config('app.url') . $value;
    }
}