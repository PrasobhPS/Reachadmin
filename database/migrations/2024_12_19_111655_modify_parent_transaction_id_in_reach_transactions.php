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
            $table->string('parent_transaction_id', 250)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_transactions', function (Blueprint $table) {
            $table->string('parent_transaction_id')->change();
        });
    }
};
