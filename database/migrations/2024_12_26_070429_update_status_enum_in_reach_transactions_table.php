<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reach_transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE `reach_transactions` CHANGE `status` `status` ENUM('Pending', 'Completed', 'Failed', 'Withdraw') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE `reach_transactions` MODIFY `status` ENUM('Pending', 'Completed', 'failed') NOT NULL");
        });
    }
};
