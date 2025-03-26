<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reach_chandlery', function (Blueprint $table) {
            $table->boolean('show_coupon_code')->default(false);
            $table->string('chandlery_coupon_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_chandlery', function (Blueprint $table) {
            $table->dropColumn('show_coupon_code');
            $table->string('chandlery_coupon_code')->nullable('false')->change();
        });
    }
};
