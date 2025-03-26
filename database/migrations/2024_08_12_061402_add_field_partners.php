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
            $table->string('partner_cover_image_mob')->after('partner_cover_image')->nullable();
            $table->string('partner_side_image_mob')->after('partner_side_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_partners', function (Blueprint $table) {
            $table->dropColumn('partner_cover_image_mob');
            $table->dropColumn('partner_side_image_mob');
        });
    }
};
