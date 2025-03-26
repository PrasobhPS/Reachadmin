<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachClubHouse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_club_house';

    protected $fillable = [
        'club_name',
        'club_short_desc',
        'club_button_name',
        'club_image',
        'club_image_mob',
        'club_status',
        'club_order',
        'club_image_thumb'
    ];
}
