<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::table('private_chat_messages', function (Blueprint $table) {
            $table->text('sent_at')->nullable();
        });
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_chat_messages', function (Blueprint $table) {
            $table->dropColumn('sent_at');
        });
    }
};
