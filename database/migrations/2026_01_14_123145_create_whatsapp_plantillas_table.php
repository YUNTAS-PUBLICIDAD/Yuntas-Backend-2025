<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('whatsapp_plantillas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('producto_id')->unique(); // Un producto tiene una sola plantilla
        $table->text('parrafo');
        $table->string('imagen_principal')->nullable();
        $table->timestamps();

        $table->foreign('producto_id')->references('id')->on('products')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_plantillas');
    }
};
