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
        Schema::table('reach_get_in_touch', function (Blueprint $table) {
            $table->string('get_in_touch_phone_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_get_in_touch', function (Blueprint $table) {
            $table->string('get_in_touch_phone_code')->nullable(false)->change();
        });
    }
};
