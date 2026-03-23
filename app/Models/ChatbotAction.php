<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotAction extends Model
{
    protected $fillable = [
      'trigger_type',
      'action_type',
      'parameters'
    ];

    protected $casts = [
      'parameters' => 'array'
    ];

    public function conditions()
    {
      return $this->hasMany(ChatbotActionCondition::class, 'action_id')->where('is_active', true);
    }

    public function intents()
    {
      return $this->belongsToMany(
        ChatbotIntent::class,
        'chatbot_intent_actions',
        'action_id',
        'intent_id'
      );
    }

    public function answers()
    {
      return $this->belongsToMany(
        ChatbotAnswer::class, 'chatbot_answer_actions', 'action_id', 'answer_id'
      );
    }
}
