<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotFlowNode extends Model
{
  protected $fillable = [
         'flow_id',
         'uuid',
         'type',
         'position',
         'message',
         'metadata',
         'options'
     ];

     protected $casts = [
         'position' => 'array',
         'metadata' => 'array',
         'options' => 'array',
     ];

     public function flow()
     {
         return $this->belongsTo(ChatbotFlow::class, 'flow_id');
     }
}
