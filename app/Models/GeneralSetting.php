<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
      'company_name',
      'company_ruc',
      'logo_light',
      'logo_dark',
      'theme'
    ];
}
