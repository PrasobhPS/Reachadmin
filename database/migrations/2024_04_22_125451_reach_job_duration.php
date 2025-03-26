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
        Schema::create('reach_job_duration', function (Blueprint $table) {
            $table->id();
            $table->string('job_duration');
            $table->enum('job_duration_status', ['A', 'I'])->default('A')->comment('A: Active, I: Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_job_duration');
    }
};
