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
        Schema::create('reach_members', function (Blueprint $table) {
            $table->id();
            $table->string('members_fname');
            $table->string('members_lname');
            $table->string('members_email');
            $table->string('members_phone')->nullable();
            $table->date('members_dob')->nullable();
            $table->text('members_address')->nullable();
            $table->string('members_payment_method')->nullable();
            $table->string('members_subscription_plan')->nullable();
            $table->timestamp('members_subscription_start_date')->nullable();
            $table->timestamp('members_subscription_end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_members');
    }
};
