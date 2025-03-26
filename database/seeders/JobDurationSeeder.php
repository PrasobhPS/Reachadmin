<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobDurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobDuration = [
            ['job_duration' => 'Permanent'],
            ['job_duration' => 'Seasonal'],
            ['job_duration' => 'Temperory'],
        ];

        // Insert data into the countries table
        DB::table('reach_job_duration')->insert($jobDuration);
    }
}
