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
        Schema::create('reach_specialist_call_rates', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('specialist_id');
            $table->json('rate');
            $table->timestamps();
            $table->foreign('specialist_id')->references('id')->on('reach_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_specialist_call_rates');
    }
};
