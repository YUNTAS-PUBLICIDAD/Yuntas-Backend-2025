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
        Schema::create('chatbot_flow_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_id')
            ->constrained('chatbot_flows')
            ->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('type'); // message | menu | action
            $table->json('position')->nullable();
             $table->text('message')->nullable();
            $table->json('metadata')->nullable(); // flags simples
            $table->json('options')->nullable(); // decisiones del nodo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_flow_nodes');
    }
};
