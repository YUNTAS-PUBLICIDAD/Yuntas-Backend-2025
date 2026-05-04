<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    use HasFactory;

    protected $fillable = [
      'event',
      'active'
    ];

    protected $casts = [
      'active' => 'boolean'
    ];

    public function actions()
    {
      return $this->hasMany(AutomationAction::class);
    }
}
