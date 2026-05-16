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
        Schema::create('template_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')
            ->constrained()
            ->cascadeOnDelete();
            $table->unsignedInteger('step')
            ->default(1);
            // 5
            $table->unsignedInteger('delay_value')
            ->default(0);

            // minutes | hours | days
            $table->string('delay_unit')
            ->default('minutes');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index([
              'template_id',
              'step'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_steps');
    }
};
