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
        Schema::table('private_chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_of')->nullable();
            $table->tinyInteger('is_edited')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_chat_messages', function (Blueprint $table) {
            $table->dropColumn('reply_of');
            $table->dropColumn('is_edited');
        });
    }
};
