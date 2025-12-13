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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action', 100)->nullable();
            $table->string('table_name', 100)->nullable();
            $table->integer('record_id')->nullable();
            $table->text('before_data')->nullable();
            $table->text('after_data')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->index('user_id');
            $table->index('table_name');
            $table->index('record_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
