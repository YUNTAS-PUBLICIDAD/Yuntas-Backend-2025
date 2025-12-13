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
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('meta_title', 70)->nullable()->after('title');
            $table->string('meta_description', 160)->nullable()->after('meta_title');
            $table->string('video_url', 255)->nullable()->after('content');
        });

        Schema::table('blog_images', function (Blueprint $table) {
            $table->string('alt_text', 255)->nullable()->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'video_url']);
        });
        Schema::table('blog_images', function (Blueprint $table) {
            $table->dropColumn('alt_text');
        });
    }
};
