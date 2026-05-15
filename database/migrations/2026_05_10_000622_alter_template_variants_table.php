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
      Schema::table('template_variants', function (Blueprint $table) {

                  // NUEVA RELACIÓN
                  $table->foreignId('template_step_id')
                      ->nullable()
                      ->after('id')
                      ->constrained()
                      ->cascadeOnDelete();

                  // YA NO NECESITAS CONTEXT
                  $table->dropColumn('context');

                  // NUEVO INDEX
                  $table->index([
                      'template_step_id',
                      'channel'
                  ]);
              });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('template_variants', function (Blueprint $table) {

      $table->string('context')
      ->nullable();

      $table->dropColumn('template_step_id');

      });
    }
};
