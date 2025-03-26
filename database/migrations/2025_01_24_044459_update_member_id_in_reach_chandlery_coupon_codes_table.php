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
        Schema::table('reach_chandlery_coupon_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_chandlery_coupon_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable(false)->default(0)->change();
        });
    }
};
