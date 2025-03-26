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
        Schema::create('reach_club_house_moderator', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->unsignedBigInteger('member_id');
            $table->foreign('club_id')->references('id')->on('reach_club_house')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('reach_members')->onDelete('cascade');
            $table->char('is_deleted', 2)->default('N');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_club_house_moderator');
    }
};
