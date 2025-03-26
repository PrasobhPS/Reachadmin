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
        Schema::table('reachMembershipPage', function (Blueprint $table) {
            $table->dropColumn(['page_header', 'page_description', 'images', 'page_slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reachMembershipPage', function (Blueprint $table) {
            $table->string('page_header'); // Adjust the type to the original one for each field
            $table->string('page_description');
            $table->string('images');
            $table->string('page_slug');
        });
    }
};
