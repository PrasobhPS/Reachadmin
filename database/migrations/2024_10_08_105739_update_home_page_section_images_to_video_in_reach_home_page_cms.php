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
        Schema::table('reach_home_page_cms', function (Blueprint $table) {
            $table->dropColumn('home_page_section_mob_images'); // Drop the old column
            $table->string('home_page_video')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_home_page_cms', function (Blueprint $table) {
            $table->dropColumn('home_page_video'); // Drop the new column
            $table->string('home_page_section_mob_images')->nullable();
        });
    }
};
