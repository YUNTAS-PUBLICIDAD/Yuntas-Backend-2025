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
        Schema::create('popup_images', function (Blueprint $table) {
            $table->id();

            // Relación
            $table->foreignId('popup_id')
            ->constrained()->cascadeOnDelete();

            // Core
            $table->string('image'); // Path o URL
            $table->string('device'); // desktop | mobile
            $table->string('slot'); // left | right | center

            // Metadata opcional
            $table->string('alt')->nullable();
            $table->string('title')->nullable();

            $table->timestamps();

            // Evita duplicados por contexto
            $table->unique(['popup_id', 'device', 'slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popup_images');
    }
};
