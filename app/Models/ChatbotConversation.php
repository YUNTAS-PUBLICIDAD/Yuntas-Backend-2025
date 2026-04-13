<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatbotConversation extends Model
{
    protected $fillable = [
      'lead_id',
      'started_at',
      'ended_at',
      'context',
      'channel',
      'external_id',
      'uuid'
    ];

    protected $casts = [
      'context' => 'array',
      'started_at' => 'datetime',
      'ended_at' => 'datetime',
      'uuid' => 'string'
    ];

    public function messages()
    {
      return $this->hasMany(ChatbotMessage::class, 'conversation_id');
    }

    public function lead()
    {
      return $this->belongsTo(Lead::class);
    }

    protected static function booted()
    {
      static::creating(function ($model) {
        $model->uuid = Str::uuid();
      });
    }
}
