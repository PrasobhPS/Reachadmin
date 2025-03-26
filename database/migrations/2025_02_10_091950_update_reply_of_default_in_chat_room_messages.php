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
        Schema::table('chat_room_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_of')->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_room_messages', function (Blueprint $table) {
            $table->integer('reply_of')->nullable(false)->default(0)->change();
        });
    }
};
