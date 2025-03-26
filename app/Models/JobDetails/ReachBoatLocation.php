<?php

namespace App\Models\JobDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachBoatLocation extends Model
{
    use HasFactory;

    protected $table = 'reach_boat_location';

    protected $fillable = [
        'boat_location',
        'boat_location_status'
        // Add other fields that are mass assignable
    ];
}
