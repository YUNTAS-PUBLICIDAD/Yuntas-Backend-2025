<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_template_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
            ->constrained()
            ->cascadeOnDelete();
            $table->foreignId('template_variant_id')
            ->constrained()
            ->cascadeOnDelete();
            $table->string('key'); // product_image | hero | banner
            $table->string('path');
            $table->timestamps();
            $table->index(['product_id', 'template_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_template_assets');
    }
};
