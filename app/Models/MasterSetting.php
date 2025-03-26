<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialist_booking_fee',
        'specialist_booking_fee_half_hour',
        'specialist_booking_fee_extra',
        'specialist_cancel_fee',
        'member_cancel_fee',
        'reach_fee',
        'full_membership_fee',
        'monthly_membership_fee',
        'referral_bonus',
        'full_membership_fee_euro',
        'full_membership_fee_dollar',
        'monthly_membership_fee_euro',
        'monthly_membership_fee_dollar',
        'specialist_booking_fee_euro',
        'specialist_booking_fee_half_hour_euro',
        'specialist_booking_fee_extra_euro',
        'specialist_booking_fee_dollar',
        'specialist_booking_fee_half_hour_dollar',
        'specialist_booking_fee_extra_dollar',
        'payment_info'
    ];
}
