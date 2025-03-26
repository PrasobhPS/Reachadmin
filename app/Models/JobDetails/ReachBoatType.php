<?php

namespace App\Models\JobDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachBoatType extends Model
{
    use HasFactory;

    protected $table = 'reach_boat_type';

    protected $fillable = [
        'boat_type',
        'boat_type_status'
        // Add other fields that are mass assignable
    ];
}
