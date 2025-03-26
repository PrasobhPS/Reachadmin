<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachQualifications extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_qualifications';
    protected $primaryKey = 'qualification_id';

    protected $fillable = [
        'qualification_name',
        'qualification_status',
    ];
}
