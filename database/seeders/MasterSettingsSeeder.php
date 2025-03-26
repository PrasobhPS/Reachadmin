<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterSettingsSeeder extends Seeder
{
    public function run()
    {
        DB::table('master_settings')->insert([
            'id' => 1,
            'specialist_booking_fee' => 100.00,
            'specialist_cancel_fee' => 25.00,
            'member_cancel_fee' => 25.00,
            'reach_fee' => 30.00,
            'created_at' => Carbon::parse('2024-06-28 17:50:41'),
            'updated_at' => Carbon::parse('2024-06-28 12:53:44'),
        ]);
    }
}

