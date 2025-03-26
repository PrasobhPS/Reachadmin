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
        Schema::create('reach_specialist_ratings', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('specialist_id'); // Specialist ID
            $table->unsignedBigInteger('member_id'); // Member ID (who gave the rating)
            $table->integer('rating')->comment('Rating out of 5'); // Rating (1 to 5)
            $table->text('review')->nullable()->comment('Optional written review'); // Review (optional)
            $table->timestamps(); // created_at and updated_at timestamps

            // Foreign key constraints
            $table->foreign('specialist_id')->references('id')->on('reach_members')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('reach_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_specialist_ratings');
    }
};
