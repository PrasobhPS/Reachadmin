<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Specialist_call_schedule extends Model
{
    use HasFactory;

    protected $table = 'reach_member_specialist_call_schedule';

    protected $fillable = [
        'member_id',
        'specialist_id',
        'call_scheduled_time',
        'call_scheduled_date',
        'call_status',
        'call_scheduled_timezone',
        'uk_scheduled_time',
        'call_fee',
        'cancel_reason',
        'call_scheduled_reason',
        'call_booking_id',
        'meeting_id',
        'meeting_link',
        'cancelled_by',
        'cancelled_on',
        'timeSlot',
        'member_rearrange',
        'extended_parent_id',
        'booking_status'
    ];

    public function specialist()
    {
        return $this->belongsTo(ReachMember::class, 'specialist_id', 'id');
    }
    public function scopeWithActiveMembers($query)
    {
        return $query->whereHas('member', function($query) {
            $query->where('members_status', 'A');
        });
    }
    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }

    public static function generateMeetingId()
    {
        do {
            $code = Str::random(20);
        } while (self::where('meeting_id', $code)->exists());

        return $code;
    }

}
