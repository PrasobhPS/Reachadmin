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
        Schema::create('reach_specialist_videos', function (Blueprint $table) {
            $table->id('video_id');
            $table->unsignedBigInteger('specialist_id');
            $table->foreign('specialist_id')->references('id')->on('reach_specialist');
            $table->string('video_title');
            $table->string('video_sub_title');
            $table->string('video_description');
            $table->string('video_file');
            $table->char('video_status', 2)->default('A');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_specialist_videos');
    }
};
