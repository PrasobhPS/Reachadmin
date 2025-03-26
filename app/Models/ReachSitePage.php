<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachSitePage extends Model
{
    use HasFactory;

    protected $table = 'reach_site_pages';

    protected $fillable = [
        'site_page_header',
        'site_page_details',
        'site_page_images',
        'site_page_video',
        'site_page_slug',
        'site_chandlery_percentage',
        'site_chandlery_coupon',
        'site_chandlery_url',
        'site_page_status',
        'site_page_type',
        'site_chandlery_logo',
        'site_chandlery_text',
        'left_side_content',
        'expert_call_title',
        'expert_call_description',
        'site_chandlery_category1',
        'site_chandlery_category2',
        'order',
        'cruz_title',
        'cruz_description'

        // Add other fields that are mass assignable
    ];
}
