<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotIntent extends Model
{
    protected $fillable = ['name', 'description'];

    public function questions()
    {
      return $this->hasMany(ChatbotQuestion::class, 'intent_id');
    }

    public function actions()
    {
      return $this->belongsToMany(ChatbotAction::class, 'chatbot_intent_actions', 'intent_id', 'action_id')->withPivot(['priority', 'is_active'])->wherePivot('is_active', true);
    }
}
