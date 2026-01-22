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
        Schema::create('whatsapp_popup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_source_id')->constrained('lead_sources')->onDelete('cascade');
            $table->string('nombre');
            $table->text('mensaje');
            $table->string('imagen_url')->nullable();
            $table->json('variables')->nullable(); // ['nombre', 'producto']
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_popup');
    }
};
