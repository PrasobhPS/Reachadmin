<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachChandlery extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_chandlery';

    protected $fillable = [
        'chandlery_name',
        'chandlery_description',
        'chandlery_coupon_code',
        'chandlery_website',
        'chandlery_image',
        'chandlery_discount',
        'chandlery_status',
        'chandlery_order',
        'chandlery_logo',
        'show_coupon_code',
    ];
}
