<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Foundation\Queue\Queueable;

class RecordProductView
{
    use Queueable;

    public function __construct(
        private readonly int $productId
    ) {}

    public function handle(): void
    {
        $product = Product::find($this->productId);

        if (!$product) {
            return;
        }

        $product->increment('views_count');

        $product->update([
            'last_viewed_at' => now(),
        ]);
    }
}