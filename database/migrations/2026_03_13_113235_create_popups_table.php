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
    Schema::create('popups', function (Blueprint $table) {
      $table->id();
      $table->string('slug')->unique();
      $table->string('title');
      $table->string('button_text');
      $table->string('image');
      $table->string('image_alt');
      $table->string('image_title')->nullable();
      $table->string('page_target');
      $table->unsignedTinyInteger('delay_seconds')->default(5);
      $table->unsignedTinyInteger('priority')->default(1);
      $table->timestamp('start_date')->nullable();
      $table->timestamp('end_date')->nullable();
      $table->boolean('active')->default(true);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('popups');
  }
};
