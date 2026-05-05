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
            $table->string('cta_text')->nullable()->after('content');
            $table->string('cta_url')->nullable()->after('cta_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_variants', function (Blueprint $table) {
            $table->dropColumn(['cta_text', 'cta_url']);
        });
    }
};
