<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachMemberRefferals extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'refferal_member_id',
        'refferal_code',
    ];

    public function refferedMember()
    {
        return $this->belongsTo(ReachMember::class, 'refferal_member_id', 'id');
    }

    public function refferalMember()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }

    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }

}
