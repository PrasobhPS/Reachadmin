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
        Schema::table('stripe_payment_transfer', function (Blueprint $table) {
            $table->string('from_currency')->nullable();
            $table->string('to_currency')->nullable();
            $table->decimal('converted_amount', 15, 2)->nullable();
            $table->decimal('exchange_rate', 15, 8)->nullable();
            $table->unsignedBigInteger('withdraw_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stripe_payment_transfer', function (Blueprint $table) {
            $table->dropColumn(['from_currency', 'to_currency', 'converted_amount', 'exchange_rate', 'withdraw_id']);
        });
    }
};
