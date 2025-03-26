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
            $table->renameColumn('parent_discount', 'original_amount_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stripe_payment_transaction', function (Blueprint $table) {
            $table->renameColumn('original_amount_paid', 'parent_discount');
        });
    }
};
