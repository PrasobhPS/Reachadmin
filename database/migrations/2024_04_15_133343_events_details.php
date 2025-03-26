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
        Schema::create('reach_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->text('event_details')->nullable();
            $table->date('event_start_date');
            $table->date('event_end_date');
            $table->string('event_allowed_members')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_events');
    }
};
