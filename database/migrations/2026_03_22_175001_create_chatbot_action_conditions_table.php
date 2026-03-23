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
        Schema::create('chatbot_action_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_id')
            ->constrained('chatbot_actions')
            ->cascadeOnDelete();
            $table->string('field', 100);
            $table->enum('operator', [
              '=',
              '!=',
              'contains',
              'not_contains',
              'in',
              '>',
              '<'
            ]);
            $table->text('value');
            $table->enum('logical_operator', ['AND', 'OR'])
            ->default('AND');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['action_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_action_conditions');
    }
};
