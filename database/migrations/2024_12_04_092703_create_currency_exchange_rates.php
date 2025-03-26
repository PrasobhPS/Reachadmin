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
        Schema::create('currency_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3);  // Currency code like USD, GBP, EUR
            $table->decimal('exchange_rate_to_usd', 15, 6);  // Exchange rate of this currency to USD
            $table->decimal('exchange_rate_to_gbp', 15, 6);  // Exchange rate of this currency to GBP
            $table->decimal('exchange_rate_to_eur', 15, 6);  // Exchange rate of this currency to EUR
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_exchange_rates');
    }
};
