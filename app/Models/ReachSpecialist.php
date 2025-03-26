<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachSpecialist extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_specialist';

    protected $fillable = [
        'specialist_fname',
        'specialist_lname',
        'specialist_email',
        'specialist_dob',
        'specialist_country',
        'specialist_region',
        'specialist_employment',
        'specialist_employment_history',
        'specialist_biography',
        'specialist_interest',
        'specialist_status',
        'specialist_video',
        'specialist_profile_picture',
        'specialist_address',
        'specialist_phone',
        'specialist_title',
        'member_id',
        // Add other fields that are mass assignable
    ];
}
