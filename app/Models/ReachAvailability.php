<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachAvailability extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_current_availability';
    protected $primaryKey = 'availability_id';

    protected $fillable = [
        'availability_name',
        'availability_status',
    ];
}
