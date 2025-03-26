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
        Schema::create('reachMembershipPage', function (Blueprint $table) {
            $table->id();
            $table->string('page_header');
            $table->text('page_description');
            $table->string('images');
            $table->string('page_slug');
            $table->string('membership_title');
            $table->text('membership_description');
            $table->string('membership_button');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reachMembershipPage');
    }
};
