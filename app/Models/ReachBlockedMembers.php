<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachBlockedMembers extends Model
{
    use HasFactory;

    protected $table = 'reach_blocked_members';

    protected $fillable = [
        'member_id',
        'blocked_by',
    ];
}
