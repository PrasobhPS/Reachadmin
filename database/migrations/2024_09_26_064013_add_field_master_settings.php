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
            $table->float('monthly_membership_fee')->after('full_membership_fee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_settings', function (Blueprint $table) {
            $table->dropColumn('monthly_membership_fee');
        });
    }
};
