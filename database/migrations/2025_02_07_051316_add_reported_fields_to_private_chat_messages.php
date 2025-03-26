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
            $table->boolean('is_reported')->default(false); // Replace 'existing_column' with a relevant column
            $table->text('reported_reason')->nullable()->after('is_reported');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_chat_messages', function (Blueprint $table) {
            $table->dropColumn(['is_reported', 'reported_reason']);
        });
    }
};
