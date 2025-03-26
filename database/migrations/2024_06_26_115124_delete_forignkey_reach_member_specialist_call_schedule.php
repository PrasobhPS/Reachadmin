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
        // Check if the foreign key exists before attempting to drop it
        $foreignKeys = $this->listTableForeignKeys('reach_member_specialist_call_schedule');
        if (in_array('reach_member_specialist_call_schedule_specialist_id_foreign', $foreignKeys)) {
            Schema::table('reach_member_specialist_call_schedule', function (Blueprint $table) {
                $table->dropForeign('reach_member_specialist_call_schedule_specialist_id_foreign');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally, you can re-add the foreign key here if needed
        Schema::table('reach_member_specialist_call_schedule', function (Blueprint $table) {
            $table->foreign('specialist_id')
                  ->references('id')
                  ->on('reach_specialist')
                  ->onDelete('cascade');
        });
    }

    /**
     * List all foreign keys for a given table.
     *
     * @param string $table
     * @return array
     */
    public function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        return array_map(function($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }
};
