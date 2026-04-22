<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Popup extends Model
{
  protected $fillable = [
    'lead_source_id',
    'slug',
    'title',
    'button_text',
    'button_color',
    'button_text_color',
    // 'image',
    // 'image_alt',
    // 'image_title',
    'page_target',
    'delay_seconds',
    'priority',
    'start_date',
    'end_date',
    'active',
    'product_id'
  ];

  protected $casts = [
    'active' => 'boolean',
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'delay_seconds' => 'integer',
    'priority' => 'integer'
  ];


  public function scopeActive(Builder $query)
  {
    return $query->where('active', true);
  }

  public function scopeForPage(Builder $query, string $page)
  {
    return $query->where(function ($q) use ($page) {
      $q->where('page_target', $page)
        ->orWhere('page_target', 'all');
    });
  }

  public function scopeInSchedule(Builder $query)
  {
    return $query->where(function ($q) {
      $q->whereNull('start_date')->orWhere('start_date', '<=', now());
    })->where(function ($q) {
      $q->whereNull('end_date')
        ->orWhere('end_date', '>=', now());
    });
  }


  protected static function booted()
  {
    static::creating(function ($popup) {
      if (empty($popup->slug)) {
        // Slug a partir del titulo, único
        $baseSlug = Str::slug($popup->title);
        $slug = $baseSlug;
        $i = 1;
        // Este while comprueba la base de datos
        while (Popup::where('slug', $slug)->exists()) {
          $slug = $baseSlug.'-'.$i++;
        }
        $popup->slug = $slug;
      }
    });
  }

  public function setButtonColorAttribute($value)
  {
    // if($value){
    //   $value = strtolower($value);
    // }
    // $this->attributes['button_color'] = $value;
    if($value !== null) {
      $this->attributes['button_color'] = strtolower(trim($value));
    }else{
      $this->attributes['button_color'] = null;
    }
  }

  public function setButtonTextColorAttribute($value)
  {
    if(!$value){
      $this->attributes['button_text_color'] = "#ffffff";
    }else {
      $this->attributes['button_text_color'] = strtolower(trim($value));
    }
  }

  public function images()
  {
    return $this->hasMany(PopupImage::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

}
