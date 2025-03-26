<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoatLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boatLocation = [
            ['boat_location' => 'Mediterranean'],
            ['boat_location' => 'Caribbean'],
            ['boat_location' => 'Africa'],
            ['boat_location' => 'Asia'],
            ['boat_location' => 'North America'],
            ['boat_location' => 'Northern Europe'],
            ['boat_location' => 'Australia'],
            ['boat_location' => 'South America'],
        ];

        // Insert data into the countries table
        DB::table('reach_boat_location')->insert($boatLocation);
    }
}
