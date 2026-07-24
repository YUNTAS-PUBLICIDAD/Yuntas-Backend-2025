<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;

class PopupImage extends Model
{
  use HasActivityLog;
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
