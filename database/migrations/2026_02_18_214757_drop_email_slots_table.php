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
        Schema::table('email_messages', function (Blueprint $table) {
            $table->dropForeign(['slot_id']); // elimina la relación
        });

        Schema::dropIfExists('email_slots');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('email_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->integer('position')->nullable();
            $table->timestamps();
        });

        Schema::table('email_messages', function (Blueprint $table) {
            $table->foreign('slot_id')
                  ->references('id')
                  ->on('email_slots')
                  ->onDelete('cascade');
        });
    }
};
