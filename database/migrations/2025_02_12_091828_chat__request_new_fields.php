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
        Schema::table('chat_requests', function (Blueprint $table) {
            $table->boolean('is_reported')->default(false);
            $table->text('reported_reason')->nullable()->after('is_reported');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_requests', function (Blueprint $table) {
            $table->dropColumn(['is_reported', 'reported_reason']);
        });
    }
};
