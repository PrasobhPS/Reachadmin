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
            $table->unsignedBigInteger('booking_id')->after('member_id')->nullable();
            $table->string('balance_transaction')->after('transfer_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stripe_payment_transfer', function (Blueprint $table) {
            $table->dropColumn('booking_id');
            $table->dropColumn('balance_transaction');
        });
    }
};
