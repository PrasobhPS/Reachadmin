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
        Schema::create('reach_chandlery', function (Blueprint $table) {
            $table->id();
            $table->string('chandlery_name');
            $table->string('chandlery_description');
            $table->string('chandlery_coupon_code', 50);
            $table->string('chandlery_website');
            $table->string('chandlery_image');
            $table->decimal('chandlery_discount', 3, 2);
            $table->char('chandlery_status', 2)->default('A');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_chandlery');
    }
};
