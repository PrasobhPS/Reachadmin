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
        Schema::table('reach_specialist', function (Blueprint $table) {
            $table->renameColumn('specialist_name', 'specialist_fname');
            $table->string('specialist_lname')->nullable();
            $table->string('specialist_profile_picture')->nullable();
            $table->string('specialist_address')->nullable();
            $table->string('specialist_phone')->nullable();
        });

        Schema::table('reach_events', function (Blueprint $table) {
            $table->string('event_picture')->nullable();
        });
        Schema::table('reach_boats', function (Blueprint $table) {
            $table->string('boat_images')->nullable();
        });
        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->string('job_images')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_specialist', function (Blueprint $table) {
            $table->renameColumn('specialist_fname', 'specialist_name');
            $table->dropColumn('specialist_lname');
            $table->dropColumn('specialist_profile_picture');
            $table->dropColumn('specialist_address');
            $table->dropColumn('specialist_phone');
        });
        Schema::table('reach_events', function (Blueprint $table) {
            $table->dropColumn('event_picture');
        });
        Schema::table('reach_boats', function (Blueprint $table) {
            $table->dropColumn('boat_images');
        });
        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->dropColumn('job_images');
        });
    }
};
