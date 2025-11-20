<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('status');
            $table->string('meta_description')->nullable()->after('meta_title');
            $table->json('keywords')->nullable()->after('meta_description');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->string('title')->nullable()->after('url');
            $table->string('alt_text')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'keywords']);
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn(['title', 'alt_text']);
        });
    }
};