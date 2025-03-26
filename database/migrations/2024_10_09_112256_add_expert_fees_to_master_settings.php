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
            $table->double('specialist_booking_fee_half_hour', 10, 2)->nullable()->after('specialist_booking_fee');
            $table->double('specialist_booking_fee_extra', 10, 2)->nullable()->after('specialist_booking_fee_half_hour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_settings', function (Blueprint $table) {
            $table->dropColumn('specialist_booking_fee_half_hour');
            $table->dropColumn('specialist_booking_fee_extra');
        });
    }
};
