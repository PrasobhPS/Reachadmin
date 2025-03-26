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
        Schema::table('reach_members', function (Blueprint $table) {
            $table->string('verification_id')->nullable();
            $table->boolean('is_doc_verified')->default(0);
            $table->timestamp('doc_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_members', function (Blueprint $table) {
            $table->dropColumn(['verification_id', 'is_doc_verified', 'doc_verified_at']);
        });
    }
};
