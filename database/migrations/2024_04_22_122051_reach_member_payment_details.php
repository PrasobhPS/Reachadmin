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
        Schema::create('reach_membership_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('Credit Card');
            $table->string('card_type'); // Visa, Mastercard, etc.
            $table->string('card_last_four'); // Last four digits of the card number
            $table->string('card_expiration_month'); // Expiration month of the card (e.g., 01 for January)
            $table->string('card_expiration_year'); // Expiration year of the card (e.g., 2024)
            $table->string('cardholder_name'); // Name on the card
            $table->string('transaction_id')->nullable();
            $table->timestamp('payment_date');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('member_id')->references('id')->on('reach_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_membership_payments');
    }
};
