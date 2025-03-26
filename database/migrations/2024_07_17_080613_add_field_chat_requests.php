<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('chat_requests', function (Blueprint $table) {
            // Check if updated_at column exists
            if (!Schema::hasColumn('chat_requests', 'updated_at')) {
                // Add the updated_at column with default current timestamp
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            }
        });
    }

    public function down()
    {
        Schema::table('chat_requests', function (Blueprint $table) {
            // Drop the updated_at column if it exists
            if (Schema::hasColumn('chat_requests', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
