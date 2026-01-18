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
        Schema::table('whatsapp_producto', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);

            $table->foreignId('producto_id')->nullable()->change();
            
            $table->foreign('producto_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_producto', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);

            $table->foreignId('producto_id')->nullable(false)->change();

            $table->foreign('producto_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }
};
