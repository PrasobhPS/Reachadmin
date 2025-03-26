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
        Schema::table('reach_site_pages', function (Blueprint $table) {
            $table->string('expert_call_title')->nullable()->after('site_chandlery_logo');
            $table->text('expert_call_description')->nullable()->after('expert_call_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_site_pages', function (Blueprint $table) {
            $table->dropColumn(['expert_call_title', 'expert_call_description']);
        });
    }
};
