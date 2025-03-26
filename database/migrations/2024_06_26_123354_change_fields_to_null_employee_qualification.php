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
            $table->string('employee_position')->nullable()->default(NULL)->change();
            $table->string('employee_vessel')->nullable()->default(NULL)->change();
            $table->string('employee_qualification')->nullable()->default(NULL)->change();
            $table->string('employee_languages')->nullable()->default(NULL)->change();
            $table->string('employee_visa')->nullable()->default(NULL)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_employee_details', function (Blueprint $table) {
            $table->string('employee_position')->change();
            $table->string('employee_vessel')->change();
            $table->string('employee_qualification')->change();
            $table->string('employee_languages')->change();
            $table->string('employee_visa')->change();
        });
    }
};
