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
            $table->renameColumn('job_title', 'job_role');
            $table->renameColumn('job_yacht_type', 'boat_type');
            $table->text('vessel_desc')->nullable();
            $table->string('vessel_size')->nullable();
            $table->unsignedBigInteger('vessel_type')->nullable();
            $table->foreign('vessel_type')->references('vessel_id')->on('reach_vessel_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->renameColumn('job_role', 'job_title');
            $table->renameColumn('boat_type', 'job_yacht_type');
            $table->dropColumn('vessel_desc');
            $table->dropColumn('vessel_size');
            $table->dropColumn('vessel_type');
        });
    }
};
