<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachJobSearch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_job_search_parameters';

    protected $fillable = [
        'search_parameter_name',
        'search_value',
        'job_id',
    ];
}
