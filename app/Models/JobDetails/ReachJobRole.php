<?php

namespace App\Models\JobDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class ReachJobRole extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $table = 'reach_job_roles';

    protected $fillable = [
        'job_role',
        'job_role_status'
        // Add other fields that are mass assignable
    ];
}
