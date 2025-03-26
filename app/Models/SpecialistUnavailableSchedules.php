<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialistUnavailableSchedules extends Model
{
    use HasFactory;
    protected $table = 'reach_member_specialist_unavailable_schedules';
    protected $fillable = [
        'member_id',
        'unavailable_time',
        'call_unavailable_date',
    ];

   public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }

}
