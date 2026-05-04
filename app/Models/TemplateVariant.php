<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateVariant extends Model
{
    use HasFactory;

    protected $fillable = [
     'template_id',
     'channel',
     'context',
     'subject',
     'content',
     'variables',
     'active'
    ];

    protected $casts = [
      'variables' => 'array',
      'active' => 'boolean'
    ];

    public function template()
    {
      return $this->belongsTo(Template::class);
    }

    public function assets()
    {
      return $this->hasMany(TemplateAsset::class);
    }

    public function productAssets()
    {
      return $this->hasMany(ProductTemplateAsset::class, 'template_variant_id');
    }

    public function actions()
    {
      return $this->hasMany(AutomationAction::class);
    }

    public function render(array $data): string
    {
      $content = $this->content;

      foreach($data as $key => $value){
        $content = str_replace("{{{$key}}}", $value, $content);
      }

      return $content;
    }
}
