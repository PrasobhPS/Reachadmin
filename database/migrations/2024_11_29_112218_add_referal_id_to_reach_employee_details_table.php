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
            $table->longtext('referrance')->nullable()->after('employee_avilable_date'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_employee_details', function (Blueprint $table) {
            $table->dropColumn('referrance');
        });
    }
};
