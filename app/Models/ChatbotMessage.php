<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotMessage extends Model
{
    protected $fillable = [
      'conversation_id',
      'message_text',
      'sender',
      'timestamp',
      'metadata'
    ];

    protected $casts = [
      'metadata' => 'array'
    ];

    public function conversation()
    {
      return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }
}
