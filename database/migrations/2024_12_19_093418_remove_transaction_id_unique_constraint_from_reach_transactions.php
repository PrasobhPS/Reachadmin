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
        Schema::table('reach_transactions', function (Blueprint $table) {
            $table->dropUnique('reach_transactions_transaction_id_unique'); // Replace with the actual index name if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_transactions', function (Blueprint $table) {
            $table->unique('transaction_id'); // Recreate the unique index if needed
        });
    }
};
