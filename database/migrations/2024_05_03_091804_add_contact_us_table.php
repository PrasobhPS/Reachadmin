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
        Schema::create('reach_get_in_touch', function (Blueprint $table) {
            $table->id();
            $table->string('get_in_touch_name_title')->nullable();
            $table->string('get_in_touch_fname');
            $table->string('get_in_touch_lname');
            $table->string('get_in_touch_email');
            $table->string('get_in_touch_phone_code');
            $table->string('get_in_touch_phone_number');
            $table->string('get_in_touch_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_get_in_touch');
    }
};
