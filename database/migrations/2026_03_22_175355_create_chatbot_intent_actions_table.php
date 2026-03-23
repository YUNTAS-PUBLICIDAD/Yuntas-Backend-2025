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
        Schema::create('chatbot_intent_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intent_id')
            ->constrained('chatbot_intents')
            ->cascadeOnDelete();
            $table->foreignId('action_id')
            ->constrained('chatbot_actions')
            ->cascadeOnDelete();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['intent_id', 'action_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_intent_actions');
    }
};
