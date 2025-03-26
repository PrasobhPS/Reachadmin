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
            $table->dropForeign(['boat_details_id']);
        });

        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->dropColumn('boat_details_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('boat_details_id')->nullable();
        });

        // Add foreign key constraint back
        Schema::table('reach_jobs', function (Blueprint $table) {
            $table->foreign('boat_details_id')->references('id')->on('reach_boats');
        });
    }
};
