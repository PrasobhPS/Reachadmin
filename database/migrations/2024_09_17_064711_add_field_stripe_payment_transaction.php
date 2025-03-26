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
        Schema::table('stripe_payment_transaction', function (Blueprint $table) {
            $table->double('discount_amount', 8, 2)->after('amount_paid')->nullable();
            $table->char('discount_type', 2)->after('discount_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stripe_payment_transaction', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_type');
        });
    }
};
