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
        Schema::table('chatbot_flow_edges', function (Blueprint $table) {
            $table->string('source_handle')->nullable()->after('from_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chatbot_flow_edges', function (Blueprint $table) {
            $table->dropColumn('source_handle');
        });
    }
};
