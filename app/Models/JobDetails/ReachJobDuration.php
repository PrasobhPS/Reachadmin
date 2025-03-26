<?php

namespace App\Models\JobDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachJobDuration extends Model
{
    use HasFactory;

    protected $table = 'reach_job_duration';

    protected $fillable = [
        'job_duration',
        'job_duration_status'
        // Add other fields that are mass assignable
    ];
}
