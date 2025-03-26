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
            $table->date('call_scheduled_date');
            $table->time('call_scheduled_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_member_specialist_call_schedule', function (Blueprint $table) {
            $table->dropColumn('call_scheduled_date');
            $table->dateTime('call_scheduled_time')->change();
        });
    }
};
