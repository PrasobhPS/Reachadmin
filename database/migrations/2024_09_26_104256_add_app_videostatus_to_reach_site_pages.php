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
        Schema::table('reach_site_pages', function (Blueprint $table) {
            $table->string('site_page_video')->nullable()->after('site_page_images');
            $table->string('site_page_type')->default('S')->comment('S: Site Page, A: App Home')->after('site_page_status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_site_pages', function (Blueprint $table) {
            Schema::dropIfExists('site_page_video');
            Schema::dropIfExists('site_page_type');
        });
    }
};
