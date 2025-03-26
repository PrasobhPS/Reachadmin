<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobRoles = [
            ['job_role' => 'Cheif Engineer'],
            ['job_role' => '2nd Engineer'],
            ['job_role' => 'Head Chef'],
            ['job_role' => 'Sous Chef'],
            ['job_role' => 'Boson'],
            ['job_role' => 'Purser'],
            ['job_role' => 'Deck Hand'],
            ['job_role' => 'Captain'],
            ['job_role' => 'First Officer'],
            ['job_role' => 'Second Officer'],
            ['job_role' => 'Cheif Steward/Stewardess'],
            ['job_role' => 'Steward/Stewardess'],
        ];

        // Insert data into the countries table
        DB::table('reach_job_roles')->insert($jobRoles);
    }
}
