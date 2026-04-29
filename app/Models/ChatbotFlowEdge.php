<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotFlowEdge extends Model
{
  protected $fillable = [
         'flow_id',
         'uuid',
         'from_uuid',
         'to_uuid',
         'label'
     ];

     public function flow()
     {
         return $this->belongsTo(ChatbotFlow::class, 'flow_id');
     }
}
