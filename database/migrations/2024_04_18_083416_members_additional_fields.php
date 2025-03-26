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
        Schema::table('reach_members', function (Blueprint $table) {
            $table->string('members_profile_picture')->nullable();
            $table->string('members_country')->nullable();
            $table->string('members_region')->nullable();
            $table->string('members_employment')->nullable();
            $table->text('members_employment_history')->nullable();
            $table->text('members_biography')->nullable();
            $table->string('members_interest')->nullable();
            $table->text('members_about_me')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_members', function (Blueprint $table) {
            $table->dropColumn('members_profile_picture');
            $table->dropColumn('members_country');
            $table->dropColumn('members_region');
            $table->dropColumn('members_employment');
            $table->dropColumn('members_employment_history');
            $table->dropColumn('members_biography');
            $table->dropColumn('members_interest');
            $table->dropColumn('members_about_me');
        });  
    }
};
