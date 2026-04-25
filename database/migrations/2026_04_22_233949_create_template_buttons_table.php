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
        Schema::create('template_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_content_id')->constrained()->cascadeOnDelete();
            $table->string('text'); // Texto del botón
            $table->string('type'); // según canal
            // $table->string('value'); // URL o payload
            $table->json('payload');
            $table->integer('order')->default(0); // Orden visual
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['template_content_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_buttons');
    }
};
