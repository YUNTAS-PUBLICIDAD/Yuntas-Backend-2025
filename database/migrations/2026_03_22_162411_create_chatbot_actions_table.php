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
        Schema::create('chatbot_actions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('trigger_type', [
              'after_answer',
              'keyword_match',
              'conversation_start',
              'conversation_end',
            ]);
            $table->enum('action_type', [
              'call_n8n',
              'call_api',
              'send_email',
              'update_context',
              'log',
              'other'
            ]);
            $table->json('parameters')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_actions');
    }
};
