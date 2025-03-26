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
        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('job_duration')->change();
            $table->foreign('job_duration')->references('id')->on('reach_job_duration');
            $table->unsignedBigInteger('job_location')->change();
            $table->foreign('job_location')->references('id')->on('reach_boat_location');
            $table->unsignedBigInteger('job_title')->change();
            $table->foreign('job_title')->references('id')->on('reach_job_roles');
            $table->unsignedBigInteger('job_yacht_type')->change();
            $table->foreign('job_yacht_type')->references('id')->on('reach_boat_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->dropForeign(['job_duration']); 
            $table->bigInteger('job_duration')->change();
            $table->dropForeign(['job_location']);
            $table->bigInteger('job_location')->change();
            $table->dropForeign(['job_title']);
            $table->bigInteger('job_title')->change();
            $table->dropForeign(['job_yacht_type']);
            $table->bigInteger('job_yacht_type')->change();
        });
    }
};
