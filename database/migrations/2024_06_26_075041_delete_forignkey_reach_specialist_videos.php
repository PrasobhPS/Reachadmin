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
        if (Schema::hasColumn('reach_specialist_videos', 'specialist_id')) {
            $foreignKeys = $this->listTableForeignKeys('reach_specialist_videos');
            if (in_array('reach_specialist_videos_specialist_id_foreign', $foreignKeys)) {
                Schema::table('reach_specialist_videos', function (Blueprint $table) {
                    $table->dropForeign('reach_specialist_videos_specialist_id_foreign');
                    $table->dropColumn('specialist_id');
                });
            } else {
                Schema::table('reach_specialist_videos', function (Blueprint $table) {
                    $table->dropColumn('specialist_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reach_specialist_videos', function (Blueprint $table) {
            $table->unsignedBigInteger('specialist_id');
            $table->foreign('specialist_id')->references('id')->on('specialists')->onDelete('cascade');
        });
    }

    /**
     * List table foreign keys.
     *
     * @param string $table
     * @return array
     */
    private function listTableForeignKeys(string $table): array
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        return array_map(function ($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }
};