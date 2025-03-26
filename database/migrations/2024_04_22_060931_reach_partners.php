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
        Schema::create('reach_partners', function (Blueprint $table) {
            $table->id();
            $table->string('partner_name');
            $table->text('partner_details');
            $table->string('partner_images')->nullable();
            $table->string('partner_unique_id')->nullable();
            $table->enum('partner_status', ['A', 'I'])->default('I')->comment('A: Active, I: Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_partners');
    }
};
