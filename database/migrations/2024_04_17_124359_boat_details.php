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
        Schema::create('reach_boats', function (Blueprint $table) {
            $table->id();
            $table->string('boat_vessel');
            $table->string('boat_location');
            $table->string('boat_type');
            $table->string('boat_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_boats');
    }
};
