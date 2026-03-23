<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatBotActionCondition extends Model
{
    protected $fillable = [
      'action_id',
      'field',
      'operatod',
      'value',
      'logical_operator',
      'is_active'
    ];
    public function action()
    {
      return $this->belongsTo(ChatbotAction::class);
    }
}
