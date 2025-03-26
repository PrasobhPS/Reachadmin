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
        Schema::create('chat_room_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('reach_members');
            $table->unsignedBigInteger('room_id');
            $table->foreign('room_id')->references('id')->on('reach_club_house');
            $table->text('content')->nullable();
            $table->text('file', 200)->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->unsignedBigInteger('reply_of');
            $table->tinyInteger('is_edited')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_room_messages');
    }
};
