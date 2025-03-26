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
        Schema::table('reach_interview_schedule', function (Blueprint $table) {
            $table->renameColumn('meeting_link', 'meeting_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_interview_schedule', function (Blueprint $table) {
            $table->renameColumn('meeting_id', 'meeting_link');
        });
    }
};
