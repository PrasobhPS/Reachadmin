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
        Schema::table('reach_employee_details', function (Blueprint $table) {
            $table->renameColumn('empolyee_role', 'employee_role');
            $table->renameColumn('empolyee_passport', 'employee_passport');
            $table->renameColumn('empolyee_avilable', 'employee_avilable');
            $table->renameColumn('empolyee_dob', 'employee_dob');
            $table->renameColumn('empolyee_gender', 'employee_gender');
            $table->renameColumn('empolyee_location', 'employee_location');
            $table->renameColumn('empolyee_position', 'employee_position');
            $table->renameColumn('empolyee_vessel', 'employee_vessel');
            $table->renameColumn('empolyee_salary_expection', 'employee_salary_expection');
            $table->renameColumn('empolyee_experience', 'employee_experience');
            $table->renameColumn('empolyee_qualification', 'employee_qualification');
            $table->renameColumn('empolyee_languages', 'employee_languages');
            $table->renameColumn('empolyee_visa', 'employee_visa');
            $table->renameColumn('empolyee_interest', 'employee_interest');
            $table->renameColumn('empolyee_last_role', 'employee_last_role');
            $table->renameColumn('empolyee_about', 'employee_about');
            $table->renameColumn('empolyee_intro', 'employee_intro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_employee_details', function (Blueprint $table) {
            $table->renameColumn('employee_role', 'empolyee_role');
            $table->renameColumn('employee_passport', 'empolyee_passport');
            $table->renameColumn('employee_avilable', 'empolyee_avilable');
            $table->renameColumn('employee_dob', 'empolyee_dob');
            $table->renameColumn('employee_gender', 'empolyee_gender');
            $table->renameColumn('employee_location', 'empolyee_location');
            $table->renameColumn('employee_position', 'empolyee_position');
            $table->renameColumn('employee_vessel', 'empolyee_vessel');
            $table->renameColumn('employee_salary_expection', 'empolyee_salary_expection');
            $table->renameColumn('employee_experience', 'empolyee_experience');
            $table->renameColumn('employee_qualification', 'empolyee_qualification');
            $table->renameColumn('employee_languages', 'empolyee_languages');
            $table->renameColumn('employee_visa', 'empolyee_visa');
            $table->renameColumn('employee_interest', 'empolyee_interest');
            $table->renameColumn('employee_last_role', 'empolyee_last_role');
            $table->renameColumn('employee_about', 'empolyee_about');
            $table->renameColumn('employee_intro', 'empolyee_intro');
        });
    }
};
