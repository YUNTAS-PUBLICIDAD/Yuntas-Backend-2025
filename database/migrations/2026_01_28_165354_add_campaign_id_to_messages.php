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
        Schema::table('email_messages', function (Blueprint $table) {
            $table->string('campaign_id')->nullable()->after('type');
            $table->index(['lead_id', 'campaign_id']);
            $table->index(['lead_id', 'type']);
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->string('campaign_id')->nullable()->after('type');
            $table->index(['lead_id', 'campaign_id']);
            $table->index(['lead_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_messages', function (Blueprint $table) {
            $table->dropIndex(['lead_id', 'campaign_id']);
            $table->dropIndex(['lead_id', 'type']);
            $table->dropColumn('campaign_id');
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropIndex(['lead_id', 'campaign_id']);
            $table->dropIndex(['lead_id', 'type']);
            $table->dropColumn('campaign_id');
        });
    }
};
