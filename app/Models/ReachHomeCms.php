<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachHomeCms extends Model
{
    use HasFactory;

    protected $table = 'reach_home_page_cms';

    protected $fillable = [
        'home_page_section_header',
        'home_page_section_details',
        'home_page_section_button',
        'home_page_section_button_link',
        'home_page_section_images',
        'home_page_section_mob_images',
        'home_page_section_status',
        'home_page_section_type',
        'home_page_video',
        'order'
         // Add other fields that are mass assignable
    ];
}
