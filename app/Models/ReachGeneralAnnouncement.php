<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachGeneralAnnouncement extends Model
{
    use HasFactory;

    protected $table = 'reach_general_announcements'; // Table name

    protected $fillable = [
        'member_type',
        'message',
        'title'
    ];
}
