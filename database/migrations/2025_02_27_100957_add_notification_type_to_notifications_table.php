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
        Schema::table('reach_notifications', function (Blueprint $table) {
            $table->tinyInteger('notification_type')->default(1)->after('is_read');
            // 0 = Admin, 1 = User, 2 = System
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_notifications', function (Blueprint $table) {
            $table->dropColumn('notification_type');
        });
    }
};
