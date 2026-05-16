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
        Schema::create('template_variant_product_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_variant_id')
            ->constrained()
            ->cascadeOnDelete();

            $table->foreignId('product_id')
            ->constrained()
            ->cascadeOnDelete();

            $table->string('subject')
            ->nullable();

            $table->longText('content')
            ->nullable();

            $table->string('cta_text')
            ->nullable();

            $table->string('cta_url')
            ->nullable();

            $table->json('variables')
            ->nullable();

            $table->json('assets')
            ->nullable();

            $table->boolean('active')
            ->default(true);
            $table->timestamps();
            $table->unique([
            'template_variant_id',
            'product_id'
            ], 'variant_product_override_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_variant_product_overrides');
    }
};
