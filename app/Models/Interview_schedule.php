<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Interview_schedule extends Model
{
    use HasFactory;

    protected $table = 'reach_interview_schedule';

    protected $fillable = [
        'employee_id',
        'job_id',
        'interview_time',
        'interview_date',
        'interview_timezone',
        'interview_uk_time',
        'interview_status',
        'meeting_id'
    ];

    public function job()
    {
        return $this->belongsTo(ReachJob::class, 'job_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(ReachEmployeeDetails::class, 'employee_id', 'employee_id');
    }

    public static function generateMeetingId()
    {
        do {
            $code = Str::random(20);
        } while (self::where('meeting_id', $code)->exists());

        return $code;
    }
}
