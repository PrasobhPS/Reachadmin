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
        Schema::create('reach_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_type');
            $table->string('template_subject');
            $table->string('template_tags');
            $table->text('template_message');
            $table->char('template_to_status', 2)->default('A');
            $table->string('template_to_address')->nullable();
            $table->string('template_cc_address')->nullable();
            $table->string('template_bcc_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_email_templates');
    }
};
