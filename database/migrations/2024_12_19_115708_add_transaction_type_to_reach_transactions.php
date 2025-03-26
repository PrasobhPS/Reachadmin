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
            $table->enum('transaction_type', ['Credit', 'Debit'])->default('Credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });
    }
};
