<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachContactUs extends Model
{
    use HasFactory;

    protected $table = 'reach_get_in_touch';

    protected $fillable = [
        'get_in_touch_name_title',
        'get_in_touch_fname',
        'get_in_touch_lname',
        'get_in_touch_phone_code',
        'get_in_touch_phone_number',
        'get_in_touch_email',
        'get_in_touch_message',
        // Add other fields that are mass assignable
    ];
}
