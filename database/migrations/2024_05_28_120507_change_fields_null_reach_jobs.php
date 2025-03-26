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
            $table->unsignedBigInteger('boat_type')->nullable()->default(NULL)->change();
            $table->unsignedBigInteger('job_duration')->nullable()->default(NULL)->change();
            $table->timestamp('job_start_date')->nullable()->default(NULL)->change();
            $table->unsignedBigInteger('job_location')->nullable()->default(NULL)->change();
            $table->text('job_summary')->nullable()->default(NULL)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('boat_type')->change();
            $table->unsignedBigInteger('job_duration')->change();
            $table->unsignedBigInteger('job_location')->change();
            $table->timestamp('job_start_date')->change();
            $table->text('job_summary')->change();
        });
    }
};
