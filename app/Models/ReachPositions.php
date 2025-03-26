<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachPositions extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_positions';
    protected $primaryKey = 'position_id';

    protected $fillable = [
        'position_name',
        'position_status',
    ];
}
