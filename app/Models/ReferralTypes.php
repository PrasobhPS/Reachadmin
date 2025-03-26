<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralTypes extends Model
{
    use HasFactory;
    protected $table = 'reach_referral_types';
    protected $fillable = ['referral_type','referral_rate'];
}
