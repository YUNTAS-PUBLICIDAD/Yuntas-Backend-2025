<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateButton extends Model
{
    protected $fillable = [
      'template_content_id',
      'text',
      'type',
      'payload',
      'order',
      'active'
    ];

    protected $casts = [
      'payload' => 'array',
      'active' => 'boolean',
      'order' => 'integer'
    ];

    // Relationships
    public function content()
    {
      return $this->belongsTo(TemplateContent::class, 'template_content_id');
    }
    // Constants
    public const TYPE_URL = 'url';
    public const TYPE_QUICK_REPLY = 'quick_reply';

    public const TYPES = [
    self::TYPE_URL,
    self::TYPE_QUICK_REPLY
    ];
}
