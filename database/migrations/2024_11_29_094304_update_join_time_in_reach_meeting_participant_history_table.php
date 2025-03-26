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
        Schema::table('reach_meeting_participant_history', function (Blueprint $table) {
            $table->timestamp('join_time')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_meeting_participant_history', function (Blueprint $table) {
            $table->timestamp('join_time')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }
};
