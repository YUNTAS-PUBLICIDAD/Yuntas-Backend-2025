<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotFlow extends Model
{
  protected $fillable = [
         'name',
         'start_node_uuid'
     ];

     // Relaciones
     public function nodes()
     {
         return $this->hasMany(ChatbotFlowNode::class, 'flow_id');
     }

     public function edges()
     {
         return $this->hasMany(ChatbotFlowEdge::class, 'flow_id');
     }

     public function triggers()
     {
       return $this->hasMany(ChatbotFlowTrigger::class, 'flow_id');
     }
}
