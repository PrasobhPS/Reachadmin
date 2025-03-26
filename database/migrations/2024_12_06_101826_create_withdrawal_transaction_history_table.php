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
        Schema::create('withdrawal_transaction_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('withdrawal_id');
            $table->string('payment_id');
            $table->string('connected_account_id');
            $table->string('from_currency', 10);
            $table->string('to_currency', 10);
            $table->decimal('exchange_rate', 10, 6);
            $table->decimal('converted_amount', 15, 2);
            $table->date('transfer_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_transaction_history');
    }
};
