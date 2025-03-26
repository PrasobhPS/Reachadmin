<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachProfileDisplayStatus extends Model
{
    use HasFactory;

    protected $table = 'reach_profile_display_status';

    protected $fillable = [
        'member_id',
        'field_name',
        'field_status',
    ];
}
