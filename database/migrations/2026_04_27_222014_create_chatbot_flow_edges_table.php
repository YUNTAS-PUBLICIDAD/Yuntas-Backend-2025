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
        Schema::create('chatbot_flow_edges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_id')
            ->constrained('chatbot_flows')
            ->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('from_uuid');
            $table->string('to_uuid');
            $table->string('label')->nullable();
            $table->timestamps();
            $table->index(['from_uuid']);
            $table->index(['to_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_flow_edges');
    }
};
