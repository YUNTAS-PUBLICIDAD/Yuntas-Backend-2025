<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotQuestion extends Model
{
    protected $fillable = ['intent_id', 'question_text', 'keywords'];

    protected $casts = [
      'keywords' => 'array',
    ];

    public function intent()
    {
      return $this->belongsTo(ChatbotIntent::class, 'intent_id');
    }

    public function answers()
    {
      return $this->hasMany(ChatbotAnswer::class, 'question_id');
    }
}
