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
            $table->date('members_subscription_start_date')->nullable()->change();
            $table->date('members_subscription_end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_members', function (Blueprint $table) {
            $table->timestamp('members_subscription_start_date')->nullable()->change();
            $table->timestamp('members_subscription_end_date')->nullable()->change();
        });
    }
};
