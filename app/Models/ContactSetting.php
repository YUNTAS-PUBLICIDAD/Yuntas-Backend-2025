<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
      'phone',
      'email',
      'address',
      'business_hours',
      'social_links',
      'whatsapp_message',
      'show_in_footer',
      'show_contact_page',
      'map_url'
    ];

    protected $casts = [
      'business_hours' => 'array',
      'social_links' => 'array',
      'show_in_footer' => 'boolean',
      'show_contact_page' => 'boolean'
    ];
}
