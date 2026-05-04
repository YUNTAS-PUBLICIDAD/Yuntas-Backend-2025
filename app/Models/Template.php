<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
protected $fillable = [
  // 'lead_source_id',
  'name',
  'active',
];

// public function leadSource()
// {
//   return $this->belongsTo(LeadSource::class);
// }

// public function contents()
// {
//   return $this->hasMany(TemplateContent::class);
// }

// Helper útil
// public function getContentByChannel($channel)
// {
//   return $this->contents()
//   ->where('channel', $channel)
//   ->where('active', true)
//   ->first();
// }

public function variants()
{
  return $this->hasMany(TemplateVariant::class);
}
}
