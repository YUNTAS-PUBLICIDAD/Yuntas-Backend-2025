<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateContent extends Model
{
    protected $fillable = [
      'template_id',
      'channel',
      'subject',
      'content',
      'image_url',
      'variables',
      'active'
    ];

    protected $casts =[
      'variables' => 'array',
      'active' => 'boolean',
    ];
    public function template()
    {
      return $this->BelongsTo(Template::class);
    }

    public function buttons()
    {
      return $this->hasMany(TemplateButton::class)->orderBy('order');
    }

    public function render(array $data): string
    {
      $message = $this->content;
      if (!$this->variables) {
        return $message;
      }
      foreach ($this->variables as $variable) {
        if (isset($data[$variable])) {
          $message = str_replace(
            "{{{$variable}}}",
            $data[$variable],
            $message
          );
        }
      }
      return $message;
    }
}
