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
        Schema::create('reach_club_house', function (Blueprint $table) {
            $table->id();
            $table->string('club_name');
            $table->string('club_button_name', 50)->nullable();;
            $table->string('club_image');
            $table->string('club_short_desc');
            $table->integer('club_order')->nullable();
            $table->char('club_status', 2)->default('A');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_club_house');
    }
};
