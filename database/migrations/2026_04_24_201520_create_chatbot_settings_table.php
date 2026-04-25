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
        Schema::create('chatbot_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(true);
            $table->string('primary_color', 7)->default('#6366F1');
            $table->string('secondary_color', 7)->nullable();
            // $table->enum('position', ['bottom-right', 'bottom-left'])->default('bottom-right');
            $table->string('icon')->nullable();
            $table->string('position')->default('bottom-right');
            $table->text('welcome_message')->nullable();
            $table->unsignedInteger('show_delay_seconds')->default(3);
            $table->unsignedInteger('auto_close_seconds')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_settings');
    }
};
