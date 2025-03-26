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
            // Check if updated_at column exists before dropping
            if (Schema::hasColumn('chat_requests', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_requests', function (Blueprint $table) {
            // Add the updated_at column back if needed
            if (!Schema::hasColumn('chat_requests', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }
};
