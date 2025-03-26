<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachPartner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_partners';

    protected $fillable = [
        'partner_name',
        'partner_details',
        'partner_cover_image',
        'partner_cover_image_mob',
        'partner_side_image',
        'partner_side_image_mob',
        'partner_logo',
        'partner_display_order',
        'partner_status',
        'partner_video',
        'partner_web_url',
        'partner_video_title',
        'video_file_type',
        'partner_video_thumb',
        'partner_coupon_code',
        'partner_discount',
        'partner_description',
        'is_chandlery',
        'partner_side_video',
        'partner_side_video_mob',
        'show_coupon_code'
    ];
}
