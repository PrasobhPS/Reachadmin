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
        Schema::create('stripe_payment_transaction', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('reach_members');
            $table->unsignedBigInteger('payment_to')->nullable();
            $table->string('stripe_payment_intend_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->double('amount_paid', 8, 2);
            $table->date('payment_date');
            $table->string('currency', 10);
            $table->string('charge_description')->nullable();
            $table->string('last_4', 4);
            $table->char('status', 1)->default('A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_payment_transaction');
    }
};
