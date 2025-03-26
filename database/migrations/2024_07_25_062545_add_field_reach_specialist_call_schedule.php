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
        Schema::table('reach_member_specialist_call_schedule', function (Blueprint $table) {
            $table->integer('cancelled_by')->nullable();
            $table->date('cancelled_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_member_specialist_call_schedule', function (Blueprint $table) {
            $table->dropColumn('cancelled_by');
            $table->dropColumn('cancelled_on');
        });
    }
};
