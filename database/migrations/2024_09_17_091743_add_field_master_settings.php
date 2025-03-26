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
            $table->float('referral_bonus')->after('reach_fee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_settings', function (Blueprint $table) {
            $table->dropColumn('referral_bonus');
        });
    }
};
