<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ReachMeetingParticipantHistory extends Model
{
    use HasFactory;
    protected $table = 'reach_meeting_participant_history';

    protected $fillable = [
        'member_id',
        'meeting_id',
        'join_time',
        'left_time',
    ];

    /**
     * Relationships
     */
    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'id');
    }
}
