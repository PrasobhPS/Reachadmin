<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reach_specialist_ratings', function (Blueprint $table) {
            $table->float('rating', 3, 1)->comment('Rating out of 5')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_specialist_ratings', function (Blueprint $table) {
            $table->integer('rating')->comment('Rating out of 5')->change();
        });
    }
};
