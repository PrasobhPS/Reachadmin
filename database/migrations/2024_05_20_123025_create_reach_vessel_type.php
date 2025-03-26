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
        Schema::create('reach_vessel_type', function (Blueprint $table) {
            $table->id('vessel_id');
            $table->string('vessel_type');
            $table->enum('vessel_status', ['A', 'I'])->default('A')->comment('A: Active, I: Inactive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_vessel_type');
    }
};
