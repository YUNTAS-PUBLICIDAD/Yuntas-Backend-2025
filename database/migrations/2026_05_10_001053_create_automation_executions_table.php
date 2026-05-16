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
        Schema::create('automation_executions', function (Blueprint $table) {
            $table->id();
            // flujo/template que está ejecutando
            $table->foreignId('template_id')
            ->constrained()
            ->cascadeOnDelete();

            // lead que recorre el flujo
            $table->foreignId('lead_id')
            ->constrained()
            ->cascadeOnDelete();

            // paso actual
            $table->unsignedInteger('current_step')
            ->default(1);

            // running | completed | failed | stopped
            $table->string('status')
            ->default('running');

            // siguiente ejecución programada
            $table->timestamp('next_run_at')
            ->nullable();

            $table->timestamp('started_at')
            ->nullable();

            $table->timestamp('finished_at')
            ->nullable();
            $table->timestamps();
            $table->index([
            'status',
            'next_run_at'
          ]);

          $table->index([
            'lead_id'
          ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_executions');
    }
};
