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
            $table->string('partner_web_url')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_partners', function (Blueprint $table) {
            $table->dropColumn('partner_web_url');
            $table->dropSoftDeletes();
        });
    }
};
