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
        Schema::create('reach_employee_details', function (Blueprint $table) {
            $table->id('employee_id');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('reach_members');
            $table->integer('empolyee_role');
            $table->integer('empolyee_passport')->nullable();
            $table->integer('empolyee_avilable')->nullable();
            $table->date('empolyee_dob')->nullable();
            $table->string('empolyee_gender', 10)->nullable();
            $table->integer('empolyee_location')->nullable();
            $table->integer('empolyee_position')->nullable();
            $table->integer('empolyee_vessel')->nullable();
            $table->integer('empolyee_salary_expection')->nullable();
            $table->integer('empolyee_experience')->nullable();
            $table->integer('empolyee_qualification')->nullable();
            $table->integer('empolyee_languages')->nullable();
            $table->integer('empolyee_visa')->nullable();
            $table->string('empolyee_interest')->nullable();
            $table->text('empolyee_last_role')->nullable();
            $table->text('empolyee_about')->nullable();
            $table->text('empolyee_intro')->nullable();
            $table->char('employee_status', 2)->default('A');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_employee_details');
    }
};
