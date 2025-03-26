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

        Schema::dropIfExists('reach_countries');

        Schema::create('reach_countries', function (Blueprint $table) {
            $table->increments('id');
            $table->char('country_iso', 2);
            $table->string('country_name', 80);
            $table->char('country_iso3', 3)->nullable();
            $table->smallinteger('country_numcode')->nullable();
            $table->integer('country_phonecode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reach_countries');
    }
};
