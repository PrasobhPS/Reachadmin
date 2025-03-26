<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachJobMedia extends Model
{
    use HasFactory;

    protected $table = 'reach_job_media';

    protected $fillable = [
        'job_id',
        'media_file',
    ];
}
