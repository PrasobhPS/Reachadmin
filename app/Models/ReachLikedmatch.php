<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachLikedmatch extends Model
{
    use HasFactory;

    protected $table = 'reach_liked_match';

    protected $fillable = [
        'employee_id',
        'job_id',
        'liked_by',
        'unliked_by',
    ];
}
