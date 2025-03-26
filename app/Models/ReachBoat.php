<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachBoat extends Model
{
    use HasFactory;

    protected $table = 'reach_boats';

    protected $fillable = [
        'boat_vessel',
        'boat_location',
        'boat_type',
        'boat_size',
        'boat_images'
        // Add other fields that are mass assignable
    ];

}
