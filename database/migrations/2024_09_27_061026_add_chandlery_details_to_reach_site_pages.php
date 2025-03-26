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
            $table->float('site_chandlery_percentage', 5, 2)->nullable()->after('site_page_slug');
            $table->string('site_chandlery_coupon')->nullable()->after('site_chandlery_percentage');
            $table->string('site_chandlery_url')->nullable()->after('site_chandlery_coupon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_site_pages', function (Blueprint $table) {
            Schema::dropIfExists('site_chandlery_percentage');
            Schema::dropIfExists('site_chandlery_coupon');
            Schema::dropIfExists('site_chandlery_url');
        });
    }
};
