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
        Schema::create('reach_site_pages', function (Blueprint $table) {
            $table->id();
            $table->string('site_page_header');
            $table->longtext('site_page_details');
            $table->string('site_page_images')->nullable();
            $table->string('site_page_unique_id')->nullable();
            $table->enum('site_page_status', ['A', 'I'])->default('A')->comment('A: Active, I: Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_site_pages');
    }
};
