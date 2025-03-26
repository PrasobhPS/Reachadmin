<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachEvents extends Model
{
    use HasFactory;

    protected $table = 'reach_events';

    protected $fillable = [
        'event_name',
        'event_details',
        'event_start_date',
        'event_end_date',
        'event_allowed_members',
        'event_picture',
        'event_members_only',
        'event_status'
        // Add other fields that are mass assignable
    ];
}
