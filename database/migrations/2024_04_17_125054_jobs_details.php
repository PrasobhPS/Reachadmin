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
        Schema::create('reach_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('boat_details_id'); // Foreign key column
            $table->foreign('boat_details_id')->references('id')->on('reach_boats');
            $table->string('job_yacht_name');
            $table->string('job_title');
            $table->string('job_yacht_type');
            $table->string('job_duration');
            $table->timestamp('job_start_date');
            $table->string('job_location');
            $table->text('job_summary')->nullable();
            $table->enum('job_status', ['A', 'I'])->default('I')->comment('A: Active, I: Inactive')->before('updated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_jobs');
    }
};
