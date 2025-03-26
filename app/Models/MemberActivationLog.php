<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberActivationLog extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'action_type', 'reason', 'created_by'];

    public function member()
    {
        return $this->belongsTo(ReachMember::class);
    }
}
