<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachSpecialistVideos extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_specialist_videos';
    protected $primaryKey = 'video_id';

    protected $fillable = [
        'member_id',
        'video_title',
        'video_sub_title',
        'video_description',
        'video_file',
        'video_file_type',
        'video_status',
        'video_thumb',
    ];

    public function getAllVideosWithSpecialists($id)
    {
        return $this->select([
                        'reach_specialist_videos.video_id',
                        'reach_specialist_videos.video_title',
                        'reach_specialist_videos.video_sub_title',
                        'reach_specialist_videos.video_file_type',
                        'reach_specialist_videos.video_file',
                        'reach_specialist_videos.video_thumb',
                        'reach_specialist_videos.video_status',
                        'reach_members.members_fname',
                        'reach_members.members_lname'
                    ])
                    ->join('reach_members', 'reach_members.id', '=', 'reach_specialist_videos.member_id')
                    ->where('reach_members.id', $id)
                    ->get();
    }

    public function getSpecialistsVideos($id)
    {
        return $this->select([
                        'reach_specialist_videos.video_id',
                        'reach_specialist_videos.video_title',
                        'reach_specialist_videos.video_sub_title',
                        'reach_specialist_videos.video_file_type',
                        'reach_specialist_videos.video_file',
                        'reach_specialist_videos.video_thumb',
                        'reach_members.members_fname',
                        'reach_members.members_lname'
                    ])
                    ->join('reach_members', 'reach_members.id', '=', 'reach_specialist_videos.member_id')
                    ->where('reach_members.id', $id)
                    ->where('reach_specialist_videos.video_status', 'A')
                    ->get();
    }
}
