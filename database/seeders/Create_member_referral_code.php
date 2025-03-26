<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReachMember;

class Create_member_referral_code extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $member_list = ReachMember::where(function($query) {
                                    $query->whereNull('referral_code')
                                          ->orWhere('referral_code', '');
                                })
                                ->where('members_type', 'M')
                                ->get();

        foreach ($member_list as $member) {
            // Generate a new referral code for each member
            $referral_code = ReachMember::generateReferralCode();

            // Update the member's referral code
            $member->update(['referral_code' => $referral_code]);
        }
    }
}
