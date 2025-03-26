<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */


     public function listTableForeignKeys($table)
     {
         $conn = Schema::getConnection()->getDoctrineSchemaManager();
     
         return array_map(function($key) {
             return $key->getName();
         }, $conn->listTableForeignKeys($table));
     }

    public function up(): void
    {
        $foreignKeys = $this->listTableForeignKeys('reach_jobs');
        if(in_array('reach_jobs_boat_details_id_foreign', $foreignKeys)) {
            Schema::table('reach_jobs', function (Blueprint $table) {
                $table->dropForeign('reach_jobs_boat_details_id_foreign');
                $table->dropColumn('boat_details_id');
            });
        } else {
            Schema::table('reach_jobs', function (Blueprint $table) {
                $table->dropColumn('boat_details_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
