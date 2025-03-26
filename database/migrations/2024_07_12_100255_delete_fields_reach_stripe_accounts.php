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
        Schema::table('reach_stripe_accounts', function (Blueprint $table) {
            $table->dropColumn('connected_client_id');
            $table->dropColumn('platform_publishable_key');
            $table->dropColumn('stripe_publishable_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_stripe_accounts', function (Blueprint $table) {
            $table->string('connected_client_id')->nullable();
            $table->string('platform_publishable_key')->nullable();
            $table->string('stripe_publishable_key')->nullable();
        });
    }
};
