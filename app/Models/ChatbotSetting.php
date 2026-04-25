<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotSetting extends Model
{
    protected $fillable = [
        'enabled',
        'primary_color',
        'secondary_color',
        'icon',
        'position',
        'welcome_message',
        'show_delay_seconds',
        'auto_close_seconds'
    ];

    protected $casts = [
      'enabled' => 'boolean'
    ];
}
