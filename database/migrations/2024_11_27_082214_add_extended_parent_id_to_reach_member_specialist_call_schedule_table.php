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
            $table->unsignedBigInteger('extended_parent_id')->nullable()->after('member_rearrange');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_member_specialist_call_schedule', function (Blueprint $table) {
            $table->dropColumn('extended_parent_id');
        });
    }
};
