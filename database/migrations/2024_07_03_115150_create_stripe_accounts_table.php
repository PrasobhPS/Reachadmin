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
        Schema::create('reach_stripe_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('connected_client_id');
            $table->string('platform_publishable_key');
            $table->string('access_token');
            $table->string('stripe_publishable_key');
            $table->string('scope');
            $table->string('refresh_token');
            $table->string('token_type');
            $table->string('stripe_user_id');
            $table->boolean('livemode');
            $table->char('status', 2)->default('A');
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_stripe_accounts');
    }
};
