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
        Schema::table('email_productos', function (Blueprint $table) {
            // Agregamos la columna 'asunto' después de 'producto_id' para mantener orden
            $table->string('asunto', 250)->nullable()->after('producto_id');
        });
    }

    public function down(): void
    {
        Schema::table('email_productos', function (Blueprint $table) {
            // Si hacemos rollback, eliminamos la columna
            $table->dropColumn('asunto');
        });
    }
};
