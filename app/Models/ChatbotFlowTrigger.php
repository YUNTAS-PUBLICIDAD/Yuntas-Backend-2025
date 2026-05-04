<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotFlowTrigger extends Model
{
   protected $fillable = [
    'flow_id',
    'type',
    'value',
    'priority',
    'is_active'
   ];

   protected $casts = [
    'is_active' => 'boolean'
   ];

   // Relación
   public function flow()
   {
     return $this->belongsTo(ChatbotFlow::class, 'flow_id');
   }
}
