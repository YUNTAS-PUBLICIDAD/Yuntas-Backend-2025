<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotAnswer extends Model
{
    protected $fillable = ['question_id', 'answer_text', 'answer_type'];

    public function question()
    {
      return $this->belongsTo(ChatbotQuestion::class, 'question_id');
    }

    public function actions()
    {
      return $this->belongsToMany(ChatbotAction::class, 'chatbot_answer_actions')->withPivot(['priority', 'is_active'])->wherePivot('is_active', true);
    }
}
