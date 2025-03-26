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
        Schema::table('reach_page_title', function (Blueprint $table) {
            $table->integer('page_step')->nullable();
            $table->string('page_step_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_page_title', function (Blueprint $table) {
            $table->dropColumn('page_step');
            $table->dropColumn('page_step_title');
        });
    }
};
