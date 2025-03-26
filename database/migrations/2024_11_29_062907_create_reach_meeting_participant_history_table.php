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
        Schema::create('reach_meeting_participant_history', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('member_id'); 
            $table->string('meeting_id', 255); 
            $table->timestamp('join_time')->nullable(); 
            $table->timestamp('left_time')->nullable(); 
            $table->timestamps();
            $table->foreign('member_id')->references('id')->on('reach_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_meeting_participant_history');
    }
};
