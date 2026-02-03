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
        'hero_title',
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

    /**
     * Trigger GitHub Action for Frontend Rebuild
     */
    public static function triggerDeployment()
    {
        $token = env('GH_TOKEN');
        $repo = env('GH_FRONTEND_REPO'); // example: owner/repo

        if (!$token || !$repo) {
            \Log::warning('Deployment trigger skipped: GH_TOKEN or GH_FRONTEND_REPO not set.');
            return;
        }

        try {
            \Illuminate\Support\Facades\Http::withToken($token)
                ->withHeaders([
                    'User-Agent' => 'Laravel-Deployment-Trigger'
                ])
                ->post("https://api.github.com/repos/{$repo}/dispatches", [
                    'event_type' => 'webhook-rebuild-frontend'
                ]);
            \Log::info("Deployment trigger sent to GitHub for repo: {$repo}");
        } catch (\Exception $e) {
            \Log::error("Failed to trigger deployment: " . $e->getMessage());
        }
    }

    protected static function booted()
    {
        static::saved(function ($product) {
            self::triggerDeployment();
        });

        static::deleted(function ($product) {
            self::triggerDeployment();
        });
    }
}
