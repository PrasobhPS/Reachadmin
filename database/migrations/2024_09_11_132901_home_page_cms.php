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
        Schema::create('reach_home_page_cms', function (Blueprint $table) {
            $table->id();
            $table->string('home_page_section_header');
            $table->enum('home_page_section_type', ['F', 'H'])->default('F')->comment('F: Full, H: Half');
            $table->text('home_page_section_details');
            $table->text('home_page_section_button');
            $table->text('home_page_section_button_link');
            $table->string('home_page_section_images')->nullable();
            $table->string('home_page_section_mob_images')->nullable();
            $table->enum('home_page_section_status', ['A', 'I'])->default('A')->comment('A: Active, I: Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_home_page_cms');
    }
};
