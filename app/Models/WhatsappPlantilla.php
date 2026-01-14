<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappPlantilla extends Model
{
    use HasFactory;


    protected $fillable = [
        'producto_id',
        'parrafo',
        'imagen_principal',
    ];

  
    public function producto()
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }
}