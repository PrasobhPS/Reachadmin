<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachJobSeen extends Model
{
    use HasFactory;

    protected $table = 'reach_job_seen_count';

    protected $fillable = [
        'job_id',
        'employee_id',
    ];
}
