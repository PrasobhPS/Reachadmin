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
        Schema::table('reach_partners', function (Blueprint $table) {
            $table->string('partner_coupon_code', 100)->nullable();
            $table->decimal('partner_discount', 5, 2)->nullable();
            $table->string('partner_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_partners', function (Blueprint $table) {
            $table->dropColumn('partner_coupon_code');
            $table->dropColumn('partner_discount');
            $table->dropColumn('partner_description');
        });
    }
};
