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
        Schema::table('master_settings', function (Blueprint $table) {
            $table->float('full_membership_fee_euro');
            $table->float('full_membership_fee_dollar');
            $table->float('monthly_membership_fee_euro');
            $table->float('monthly_membership_fee_dollar');
            $table->float('specialist_booking_fee_euro');
            $table->float('specialist_booking_fee_half_hour_euro');
            $table->float('specialist_booking_fee_extra_euro');
            $table->float('specialist_booking_fee_dollar');
            $table->float('specialist_booking_fee_half_hour_dollar');
            $table->float('specialist_booking_fee_extra_dollar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_settings', function (Blueprint $table) {
            $table->dropColumn('full_membership_fee_euro',
            'full_membership_fee_dollar',
            'monthly_membership_fee_euro',
            'monthly_membership_fee_dollar',
            'specialist_booking_fee_euro',
            'specialist_booking_fee_half_hour_euro',
            'specialist_booking_fee_extra_euro',
            'specialist_booking_fee_dollar',
            'specialist_booking_fee_half_hour_dollar',
            'specialist_booking_fee_extra_dollar');
        });
    }
};
