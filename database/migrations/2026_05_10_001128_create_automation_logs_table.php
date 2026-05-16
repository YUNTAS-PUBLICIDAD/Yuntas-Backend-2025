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
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_execution_id')
            ->constrained()
            ->cascadeOnDelete();

            $table->foreignId('template_step_id')
            ->constrained()
            ->cascadeOnDelete();

              $table->foreignId('template_variant_id')
              ->constrained()
              ->cascadeOnDelete();

              $table->foreignId('lead_id')
              ->constrained()
              ->cascadeOnDelete();

              // whatsapp | email
              $table->string('channel');

              // success | failed
              $table->string('status')
              ->default('success');

            // respuesta del provider
            $table->json('response')
            ->nullable();

            $table->text('error')
            ->nullable();

            $table->timestamp('sent_at')
            ->nullable();
            $table->timestamps();
            $table->index([
            'lead_id',
            'status'
            ]);

            $table->index([
            'channel'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
    }
};
