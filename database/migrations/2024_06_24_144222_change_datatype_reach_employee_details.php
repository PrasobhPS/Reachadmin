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
            $table->string('employee_role', 100)->change();
            $table->string('employee_position', 100)->change();
            $table->string('employee_vessel', 100)->change();
            $table->string('employee_qualification', 100)->change();
            $table->string('employee_languages', 100)->change();
            $table->string('employee_visa', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_employee_details', function (Blueprint $table) {
            $table->integer('employee_role')->change();
            $table->integer('employee_position')->change();
            $table->integer('employee_vessel')->change();
            $table->integer('employee_vessel')->change();
            $table->integer('employee_qualification')->change();
            $table->integer('employee_languages')->change();
            $table->integer('employee_visa')->change();
        });
    }
};
