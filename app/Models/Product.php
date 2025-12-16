<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Import Swagger Annotations
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Product",
 *   type="object",
 *   title="Producto",
 *   description="Modelo del producto dentro del sistema",
 *   required={"name", "slug", "price", "status"},
 *
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Laptop Lenovo Ideapad"),
 *   @OA\Property(property="slug", type="string", example="laptop-lenovo-ideapad"),
 *   @OA\Property(property="short_description", type="string", example="Laptop potente para oficina"),
 *   @OA\Property(property="description", type="string", example="Laptop con procesador AMD Ryzen..."),
 *
 *   @OA\Property(property="price", type="number", format="float", example=2599.90),
 *   @OA\Property(property="status", type="string", example="active"),
 *
 *   @OA\Property(property="meta_title", type="string", example="Laptop Lenovo - Oferta"),
 *   @OA\Property(property="meta_description", type="string", example="Mejor laptop calidad-precio"),
 *
 *   @OA\Property(
 *       property="keywords",
 *       type="array",
 *       @OA\Items(type="string"),
 *       example={"laptop", "lenovo", "computadora"}
 *   )
 * )
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'status',
        'meta_title',
        'meta_description',
        'keywords'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'keywords' => 'array',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function contentItems(): HasMany
    {
        return $this->hasMany(ProductContentItem::class);
    }

    public function contentTexts(): HasMany
    {
        return $this->hasMany(ProductContentText::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }
}
