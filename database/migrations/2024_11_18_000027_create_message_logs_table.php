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
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->string('module', 50);
            $table->integer('message_id')->nullable();
            $table->string('event', 100);
            $table->timestamp('created_at')->nullable();
            
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            
            $table->index('lead_id');
            $table->index('module');
            $table->index('event');
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
