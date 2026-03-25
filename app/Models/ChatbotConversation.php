<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class ChatbotConversation extends Model
{
    protected $fillable = [
      'lead_id',
      'started_at',
      'ended_at',
      'context'
    ];

    protected $casts = [
      'context' => 'array'
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
