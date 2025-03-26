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
        Schema::table('reach_specialist', function (Blueprint $table) {
            $table->enum('specialist_status', ['A', 'I'])->default('I')->comment('A: Active, I: Inactive')->before('updated_at');
            $table->string('specialist_video')->nullable()->before('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_specialist', function (Blueprint $table) {
            $table->dropColumn('specialist_status');
            $table->dropColumn('specialist_video');
        });    
    }
};
