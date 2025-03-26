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
        Schema::create('reach_general_announcements', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('member_type'); // e.g., 'free', 'premium', 'all'
            $table->text('message'); // Announcement message
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_general_announcements');
    }
};
