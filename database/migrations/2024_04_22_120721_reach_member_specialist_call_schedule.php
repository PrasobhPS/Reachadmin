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
        Schema::create('reach_member_specialist_call_schedule', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('specialist_id');
            $table->dateTime('call_scheduled_time');
            $table->string('call_status')->default('pending');
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('reach_members')->onDelete('cascade');
            $table->foreign('specialist_id')->references('id')->on('reach_specialist')->onDelete('cascade');
        });    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_member_specialist_call_schedule');
    }
};
