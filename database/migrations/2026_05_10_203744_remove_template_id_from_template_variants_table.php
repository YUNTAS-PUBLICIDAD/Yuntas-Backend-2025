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
          // =========================
                    // ELIMINAR FK
                    // =========================

                    $table->dropForeign([
                        'template_id'
                    ]);

                    // =========================
                    // ELIMINAR INDEX VIEJO
                    // =========================

                    $table->dropIndex([
                        'template_id',
                        'channel',
                        'context'
                    ]);

                    // =========================
                    // ELIMINAR SOLO template_id
                    // =========================

                    $table->dropColumn(
                        'template_id'
                    );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_variants', function (Blueprint $table) {
          Schema::table('template_variants', function (Blueprint $table) {

                     $table->foreignId('template_id')
                         ->nullable()
                         ->constrained()
                         ->cascadeOnDelete();

                     // recrear índice viejo
                     $table->index([
                         'template_id',
                         'channel',
                         'context'
                     ]);
                 });
        });
    }
};
