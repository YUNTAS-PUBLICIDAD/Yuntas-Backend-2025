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
        Schema::create('template_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')
            ->constrained()->cascadeOnDelete();
            $table->string('channel'); // email | whatsapp | sms
            $table->string('context'); // campaign | product |order | lead | generic
            $table->string('subject')->nullable();
            $table->longText('content');
            $table->json('variables')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['template_id', 'channel', 'context']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_variants');
    }
};
