<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachExperience extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_experience';
    protected $primaryKey = 'experience_id';

    protected $fillable = [
        'experience_name',
        'experience_status',
    ];
}
