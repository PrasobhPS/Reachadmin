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
            $table->enum('members_type', ['M', 'T', 'N'])->default('N')->comment('M: Member, T: Trial Member, N: Non Member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_members', function (Blueprint $table) {
            $table->dropColumn('members_type');
        });
    }
};
