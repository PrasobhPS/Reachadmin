<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachVesselType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_vessel_type';
    protected $primaryKey = 'vessel_id';

    protected $fillable = [
        'vessel_type',
        'vessel_status',
    ];
}
