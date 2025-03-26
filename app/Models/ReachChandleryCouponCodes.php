<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachChandleryCouponCodes extends Model
{
    use HasFactory;
    protected $table = 'reach_chandlery_coupon_codes';

    protected $fillable = [
        'chandlery_id',
        'coupon_code',
        'member_id',
        'status',
        'created_at',
        'updated_at'
    ];
}
