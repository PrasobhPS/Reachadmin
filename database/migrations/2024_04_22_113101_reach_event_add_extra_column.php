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
        Schema::table('reach_events', function (Blueprint $table) {
            $table->enum('event_members_only', ['Y', 'N'])->default('N')->comment('Y: Yes, N: No')->before('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('reach_events', function (Blueprint $table) {
            $table->dropColumn('event_members_only');
        });
    }
};
