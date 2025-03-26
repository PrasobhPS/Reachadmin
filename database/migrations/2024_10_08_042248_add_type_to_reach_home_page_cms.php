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
            $table->string('type')->default('Home')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_home_page_cms', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
