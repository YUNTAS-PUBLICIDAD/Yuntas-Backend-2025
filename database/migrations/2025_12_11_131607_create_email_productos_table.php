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
        Schema::create('email_productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')->constrained('products')->onDelete('cascade');

            $table->string('titulo', 250);

            $table->string('parrafo1', 250)->nullable();

            $table->string('imagen_principal', 250)->nullable();

            $table->longText('imagenes_secundarias')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_productos');
    }
};