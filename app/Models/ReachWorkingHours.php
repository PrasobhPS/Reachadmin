<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachWorkingHours extends Model
{
    use HasFactory;

    protected $table = 'reach_working_hours';

    protected $fillable = [
        'member_id',
        'days',
        'working_hours',
        
    ];

    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }
}
