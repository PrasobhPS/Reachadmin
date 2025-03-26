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
        Schema::create('reach_specialist', function (Blueprint $table) {
            $table->id();
            $table->string('specialist_name');
            $table->string('specialist_email');
            $table->string('specialist_password')->nullable();
            $table->date('specialist_dob');
            $table->string('specialist_country');
            $table->string('specialist_region');
            $table->string('specialist_employment');
            $table->text('specialist_employment_history');
            $table->text('specialist_biography');
            $table->string('specialist_interest');
            $table->text('specialist_about_me');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_specialist');
    }
};
