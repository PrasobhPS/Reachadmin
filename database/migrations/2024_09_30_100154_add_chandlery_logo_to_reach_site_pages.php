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
            $table->string('site_chandlery_category1')->nullable()->after('site_page_slug');
            $table->string('site_chandlery_category2')->nullable()->after('site_chandlery_category1');
            $table->string('site_chandlery_logo')->nullable()->after('site_chandlery_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_site_pages', function (Blueprint $table) {
            Schema::dropIfExists('site_chandlery_category1');
            Schema::dropIfExists('site_chandlery_category2');
            Schema::dropIfExists('site_chandlery_logo');
        });
    }
};
