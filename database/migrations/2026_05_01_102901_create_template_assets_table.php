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
        Schema::create('template_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_variant_id')
            ->constrained()->cascadeOnDelete();
            $table->string('key'); // hero | product_image | banner | logo
            $table->string('path');
            // storage path o URL
            $table->json('meta')->nullable(); // width, height, alt, position, etc
            $table->timestamps();
            $table->index(['template_variant_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_assets');
    }
};
