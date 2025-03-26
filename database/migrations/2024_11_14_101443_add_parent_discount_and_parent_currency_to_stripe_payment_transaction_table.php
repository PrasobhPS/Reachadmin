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
            $table->decimal('parent_discount', 10, 2)->nullable();
            $table->string('parent_currency', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stripe_payment_transaction', function (Blueprint $table) {
            $table->dropColumn(['parent_discount', 'parent_currency']);
        });
    }
};
