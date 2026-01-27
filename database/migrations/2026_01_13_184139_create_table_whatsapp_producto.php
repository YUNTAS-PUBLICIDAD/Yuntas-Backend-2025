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

        Schema::create('whatsapp_producto', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')->constrained('products')->onDelete('cascade');

            $table->string('parrafo', 250)->nullable();

            $table->string('imagen_principal', 250)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_producto');
    }
};
