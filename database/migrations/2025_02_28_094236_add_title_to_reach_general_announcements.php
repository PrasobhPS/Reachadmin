<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('reach_general_announcements', function (Blueprint $table) {
            $table->string('title')->after('id'); // Adding the title column
        });
    }

    public function down()
    {
        Schema::table('reach_general_announcements', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
