<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoatTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boatType = [
            ['boat_type' => 'Private'],
            ['boat_type' => 'Charter'],
            ['boat_type' => 'Other'],
        ];

        // Insert data into the countries table
        DB::table('reach_boat_type')->insert($boatType);
    }
}
