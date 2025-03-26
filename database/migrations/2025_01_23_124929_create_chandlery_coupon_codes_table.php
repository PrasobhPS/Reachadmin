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
        Schema::create('chandlery_coupon_codes', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('chandlery_id'); // Foreign key for chandlery
            $table->string('coupon_code'); // Coupon code
            $table->unsignedBigInteger('member_id'); // Foreign key for member
            $table->string('status'); // Status of the coupon code
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chandlery_coupon_codes');
    }
};
