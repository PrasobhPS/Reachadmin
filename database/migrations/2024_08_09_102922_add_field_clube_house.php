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
        Schema::table('reach_club_house', function (Blueprint $table) {
            $table->string('club_image_mob')->after('club_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_club_house', function (Blueprint $table) {
            $table->dropColumn('club_image_mob');
        });
    }
};
