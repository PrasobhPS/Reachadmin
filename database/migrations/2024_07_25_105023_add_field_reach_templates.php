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
        Schema::table('reach_email_templates', function (Blueprint $table) {
            $table->string('template_title')->nullable();
            $table->char('template_status', 1)->default('A');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_email_templates', function (Blueprint $table) {
            $table->dropColumn('template_title');
            $table->dropColumn('template_status');
        });
    }
};
