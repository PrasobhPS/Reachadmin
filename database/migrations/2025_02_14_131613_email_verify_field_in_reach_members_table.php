<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reach_members', function (Blueprint $table) {
            $table->boolean('is_email_verified')->default(0);
            $table->string('email_verify_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_members', function (Blueprint $table) {
            $table->dropColumn('is_email_verified');
            $table->dropColumn('email_verify_token');
        });
    }
};
