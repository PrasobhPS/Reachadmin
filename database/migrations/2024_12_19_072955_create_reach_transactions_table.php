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
        Schema::create('reach_transactions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('transaction_id')->unique(); // Unique transaction identifier
            $table->unsignedBigInteger('payment_id')->nullable(); // Optional payment ID
            $table->unsignedBigInteger('member_id'); // Member ID
            $table->unsignedBigInteger('payment_to')->nullable(); // Payee ID
            $table->unsignedBigInteger('parent_transaction_id')->nullable(); // Parent transaction for refunds
            $table->decimal('original_amount', 15, 2); // Original transaction amount
            $table->decimal('reduced_amount', 15, 2)->nullable(); // Reduced amount if applicable
            $table->decimal('actual_amount', 15, 2); // Final transaction amount
            $table->string('from_currency', 3); // Currency of the original amount (ISO 4217)
            $table->string('to_currency', 3); // Currency of the final amount (ISO 4217)
            $table->decimal('rate', 10, 6)->nullable(); // Conversion rate if applicable
            $table->dateTime('payment_date'); // Transaction payment date
            $table->enum('status', ['Pending', 'Completed', 'failed'])->default('completed'); // Transaction status
            $table->enum('type', ['Refunded','Referral','Membership','Book A Call','Withdraw'])->default('Membership'); 
            $table->text('description');
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_transactions');
    }
};
