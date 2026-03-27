<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupImage extends Model
{
    protected $fillable = [
      'popup_id',
      'image',
      'device',
      'slot',
      'alt',
      'title'
    ];

    public function popup()
    {
      return $this->belongsTo(Popup::class);
    }
}
