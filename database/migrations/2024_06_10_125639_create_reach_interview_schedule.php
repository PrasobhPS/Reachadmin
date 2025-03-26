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
        Schema::create('reach_interview_schedule', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('job_id');
            $table->foreign('employee_id')->references('employee_id')->on('reach_employee_details')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('reach_jobs')->onDelete('cascade');
            $table->time('interview_time');
            $table->date('interview_date');
            $table->string('interview_timezone', 20)->nullable();
            $table->time('interview_uk_time');
            $table->char('interview_status', 2)->default('P');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_interview_schedule');
    }
};
