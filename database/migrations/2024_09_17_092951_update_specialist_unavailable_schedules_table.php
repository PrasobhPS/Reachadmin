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
        Schema::table('reach_member_specialist_unavailable_schedules', function (Blueprint $table) {
            $table->longtext('unavailable_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_member_specialist_unavailable_schedules', function (Blueprint $table) {
            //
        });
    }
};
