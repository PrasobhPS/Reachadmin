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
        Schema::create('stripe_withdrawal_transaction', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('reach_members');
            $table->string('transaction_id')->nullable();
            $table->string('account_id')->nullable();
            $table->decimal('transfer_amount', 10, 2);
            $table->string('currency', 5)->nullable();
            $table->date('transfer_date');
            $table->char('status', 1)->default('A');
            $table->text('failed_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_withdrawal_transaction');
    }
};
