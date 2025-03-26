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
        Schema::table('reach_schedule_timings', function (Blueprint $table) {
            $table->renameColumn('time_slot', 'unavailable_time');
            $table->longText('time_slot')->nullable()->change();
            $table->dropColumn('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_schedule_timings', function (Blueprint $table) {
            $table->renameColumn('unavailable_time', 'time_slot');
            $table->integer('time_slot')->change();
            $table->time('start_time')->nullable();
        });
    }
};
