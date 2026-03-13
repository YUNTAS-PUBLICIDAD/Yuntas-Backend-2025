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
    Schema::create('chatbot_conversations', function (Blueprint $table) {
      $table->id();
      $table->string('session_id');
      // $table->string('role');
      // $table->text('message');
      // $table->json('payload')->nullable();
      $table->json('data');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('chatbot_conversations');
  }
};
