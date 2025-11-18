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
        Schema::create('email_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('slot_id')->nullable();
            $table->string('subject', 191);
            $table->text('body');
            $table->enum('status', ['pendiente', 'espera', 'enviado', 'fallido']);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('slot_id')->references('id')->on('email_slots')->onDelete('cascade');
            
            $table->index('lead_id');
            $table->index('slot_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_messages');
    }
};
