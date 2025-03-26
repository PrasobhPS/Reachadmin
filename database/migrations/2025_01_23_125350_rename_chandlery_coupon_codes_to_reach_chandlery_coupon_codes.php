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
           
                Schema::rename('chandlery_coupon_codes', 'reach_chandlery_coupon_codes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::rename('reach_chandlery_coupon_codes', 'chandlery_coupon_codes');
}
};
