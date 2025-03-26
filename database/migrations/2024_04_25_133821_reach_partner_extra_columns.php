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
        Schema::table('reach_partners', function (Blueprint $table) {
            $table->renameColumn('partner_images', 'partner_side_image');
            $table->string('partner_cover_image')->nullable();
            $table->string('partner_logo')->nullable();
            $table->integer('partner_display_order')->nullable();
            $table->string('partner_video')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_partners', function (Blueprint $table) {
            $table->renameColumn('partner_side_image', 'partner_images');
            $table->dropColumn('partner_cover_image');
            $table->dropColumn('partner_logo');
            $table->dropColumn('partner_display_order');
            $table->dropColumn('partner_video');
        });
    }
};
