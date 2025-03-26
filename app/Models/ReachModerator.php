<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachModerator extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_club_house_moderator';

    protected $fillable = [
        'club_id',
        'member_id',
        'is_deleted',
    ];

    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }

    public function club()
    {
        return $this->belongsTo(ReachClubHouse::class, 'club_id', 'id');
    }
}
