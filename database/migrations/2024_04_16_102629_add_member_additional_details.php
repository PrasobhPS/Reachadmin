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
            $table->enum('members_status', ['A', 'I'])->default('I')->comment('A: Active, I: Inactive');
            $table->enum('members_payment_status', ['A', 'I'])->default('I')->comment('A: Active, I: Inactive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_members', function (Blueprint $table) {
            $table->dropColumn('members_status');
            $table->dropColumn('members_payment_status');
        });
    }
};
