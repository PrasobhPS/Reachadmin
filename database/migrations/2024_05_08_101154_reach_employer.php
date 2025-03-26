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
        Schema::create('reach_employers', function (Blueprint $table) {
            $table->id('employer_id');
            $table->string('employer_company_name');
            $table->string('employer_email')->unique();
            $table->string('employer_phone');
            $table->string('employer_country');
            $table->string('employer_vessel_name')->nullable();
            $table->string('employer_profile_picture')->nullable();
            $table->char('employer_status', 2)->default('A');
            $table->timestamps();
            $table->char('is_deleted', 2)->default('N');
            $table->softDeletes('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_employers');
    }
};
