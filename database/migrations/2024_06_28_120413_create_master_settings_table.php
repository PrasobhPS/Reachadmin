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
        Schema::create('master_settings', function (Blueprint $table) {
            $table->id();
            $table->float('specialist_booking_fee');
            $table->float('specialist_cancel_fee');
            $table->float('member_cancel_fee');
            $table->float('reach_fee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_settings');
    }
};
