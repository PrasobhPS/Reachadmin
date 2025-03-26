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
            $table->string('job_salary_type', 20)->nullable();
            $table->string('job_currency', 20)->nullable();
            $table->decimal('job_salary_amount', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->dropColumn('job_salary_type');
            $table->dropColumn('job_currency');
            $table->dropColumn('job_salary_amount');
        });
    }
};
