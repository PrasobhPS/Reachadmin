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
            $table->double('specialist_amount', 8, 2)->nullable()->after('parent_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stripe_payment_transaction', function (Blueprint $table) {
            $table->dropColumn('specialist_amount');
        });
    }
};
