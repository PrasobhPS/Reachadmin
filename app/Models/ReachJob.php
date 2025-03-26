<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\JobDetails\ReachJobDuration;
use App\Models\JobDetails\ReachBoatLocation;
use App\Models\JobDetails\ReachJobRole;
use App\Models\JobDetails\ReachBoatType;

class ReachJob extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_jobs';

    protected $fillable = [
        'job_role',
        'boat_type',
        'job_duration',
        'job_start_date',
        'job_location',
        'job_summary',
        'job_status',
        'job_images',
        'member_id',
        'vessel_desc',
        'vessel_size',
        'vessel_type',
        'job_salary_type',
        'job_currency',
        'job_salary_amount',
        'job_seen_count',
        'salary_type'
        // Add other fields that are mass assignable
    ];
    public function scopeWithActiveMembers($query)
    {
        return $query->whereHas('member', function ($query) {
            $query->where('members_status', 'A');
        });
    }

    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id');
    }

    public function role()
    {
        return $this->belongsTo(ReachJobRole::class, 'job_role');
    }

    public function location()
    {
        return $this->belongsTo(ReachBoatLocation::class, 'job_location', 'id');
    }

    public function duration()
    {
        return $this->belongsTo(ReachJobDuration::class, 'job_duration', 'id');
    }

    public function boat()
    {
        return $this->belongsTo(ReachBoatType::class, 'boat_type', 'id');
    }

    public function vessel()
    {
        return $this->belongsTo(ReachVesselType::class, 'vessel_type', 'vessel_id');
    }

    public function search()
    {
        return $this->belongsTo(ReachJobSearch::class, 'id', 'job_id');
    }

    public function liked()
    {
        return $this->hasMany(ReachLikedmatch::class, 'job_id', 'id');
    }

    public static function getJobDetailsById($jobId)
    {
        return self::select([
            'reach_jobs.id',
            'reach_members.members_fname',
            'reach_members.members_lname',
            'reach_job_roles.job_role',
            'reach_job_duration.job_duration',
            'reach_boat_location.boat_location',
            'reach_boat_type.boat_type',
            'reach_jobs.vessel_desc',
            'reach_vessel_type.vessel_type',
            'reach_jobs.vessel_size',
            'reach_jobs.job_currency',
            'reach_jobs.job_salary_amount',
            'reach_jobs.job_start_date',
            'reach_jobs.job_summary',
        ])
            ->join('reach_members', 'reach_jobs.member_id', '=', 'reach_members.id')
            ->join('reach_job_roles', 'reach_jobs.job_role', '=', 'reach_job_roles.id', 'left')
            ->join('reach_job_duration', 'reach_jobs.job_duration', '=', 'reach_job_duration.id', 'left')
            ->join('reach_boat_location', 'reach_jobs.job_location', '=', 'reach_boat_location.id', 'left')
            ->join('reach_boat_type', 'reach_jobs.boat_type', '=', 'reach_boat_type.id', 'left')
            ->join('reach_vessel_type', 'reach_jobs.vessel_type', '=', 'reach_vessel_type.vessel_id', 'left')
            ->where('reach_jobs.id', $jobId)
            ->first();
    }
}
